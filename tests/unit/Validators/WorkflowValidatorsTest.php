<?php

require_once __DIR__ . '/../../Fixtures/Validators/ValidatorTestCase.php';
require_once __DIR__ . '/../../Fixtures/Commands/FakeWorkflowRepositories.php';

use Congraph\Core\Exceptions\BadRequestException;
use Congraph\Core\Exceptions\NotFoundException;
use Congraph\Core\Exceptions\ValidationException;
use Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowGetCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand;
use Congraph\Workflows\Repositories\WorkflowRepository;
use Congraph\Workflows\Validators\Workflows\WorkflowCreateValidator;
use Congraph\Workflows\Validators\Workflows\WorkflowDeleteValidator;
use Congraph\Workflows\Validators\Workflows\WorkflowFetchValidator;
use Congraph\Workflows\Validators\Workflows\WorkflowGetValidator;
use Congraph\Workflows\Validators\Workflows\WorkflowUpdateValidator;

final class WorkflowValidatorsTest extends ValidatorTestCase
{
	public function testWorkflowCreateValidatorRemovesUnexpectedFields(): void
	{
		$validator = new WorkflowCreateValidator();
		$command = new WorkflowCreateCommand($this->app->make(WorkflowRepository::class));
		$command->setParams([
			'name' => 'Basic Publishing',
			'description' => 'Testing workflows',
			'extra' => 'remove-me',
		]);

		$validator->validate($command);

		$this->assertSame([
			'name' => 'Basic Publishing',
			'description' => 'Testing workflows',
		], $command->params);
	}

	public function testWorkflowCreateValidatorThrowsForInvalidParams(): void
	{
		$this->expectException(ValidationException::class);

		$validator = new WorkflowCreateValidator();
		$command = new WorkflowCreateCommand($this->app->make(WorkflowRepository::class));
		$command->setParams(['name' => '']);

		$validator->validate($command);
	}

	public function testWorkflowGetValidatorNormalizesPagingAndSort(): void
	{
		$validator = new WorkflowGetValidator();
		$command = new WorkflowGetCommand(new FakeWorkflowRepository());
		$command->setParams([
			'filter' => ['name' => 'Basic Publishing'],
			'offset' => '5',
			'limit' => '10',
			'sort' => '-created_at',
		]);

		$validator->validate($command);

		$this->assertSame(5, $command->params['offset']);
		$this->assertSame(10, $command->params['limit']);
		$this->assertSame(['-created_at'], $command->params['sort']);
	}

	public function testWorkflowGetValidatorRejectsInvalidFilter(): void
	{
		$this->expectException(BadRequestException::class);

		$validator = new WorkflowGetValidator();
		$command = new WorkflowGetCommand(new FakeWorkflowRepository());
		$command->setParams([
			'filter' => ['invalid' => 'value'],
		]);

		$validator->validate($command);
	}

	public function testWorkflowDeleteValidatorDelegatesToRepositoryFetch(): void
	{
		$repository = new FakeWorkflowRepository();
		$validator = new WorkflowDeleteValidator($repository);
		$command = new WorkflowDeleteCommand($repository, new FakeWorkflowPointRepository(), new FakeWorkflowStepRepository());
		$command->setId(7);

		$validator->validate($command);

		$this->assertSame([['fetch', [7, []]]], $repository->calls);
	}

	public function testWorkflowFetchValidatorBubblesRepositoryErrors(): void
	{
		$this->expectException(NotFoundException::class);

		$repository = new FakeWorkflowRepository();
		$repository->fetchException = new NotFoundException(['Missing workflow']);
		$validator = new WorkflowFetchValidator($repository);
		$command = new WorkflowFetchCommand($repository);
		$command->setId(999);

		$validator->validate($command);
	}

	public function testWorkflowUpdateValidatorAllowsValidChanges(): void
	{
		$repository = $this->app->make(WorkflowRepository::class);
		$validator = new WorkflowUpdateValidator($repository);
		$command = new WorkflowUpdateCommand($repository);
		$command->setId(1);
		$command->setParams([
			'name' => 'Updated workflow',
			'description' => '',
		]);

		$validator->validate($command);

		$this->assertSame('Updated workflow', $command->params['name']);
	}
}
