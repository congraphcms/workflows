<?php

require_once __DIR__ . '/../../Fixtures/Validators/ValidatorTestCase.php';
require_once __DIR__ . '/../../Fixtures/Commands/FakeWorkflowRepositories.php';

use Congraph\Core\Exceptions\BadRequestException;
use Congraph\Core\Exceptions\NotFoundException;
use Congraph\Core\Exceptions\ValidationException;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand;
use Congraph\Workflows\Repositories\WorkflowPointRepository;
use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointCreateValidator;
use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointDeleteValidator;
use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointFetchValidator;
use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointGetValidator;
use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointUpdateValidator;

final class WorkflowPointValidatorsTest extends ValidatorTestCase
{
	protected function seeders(): array
	{
		return [
			Database\Seeders\WorkflowTestDbSeeder::class,
			Database\Seeders\WorkflowPointTestDbSeeder::class,
		];
	}

	public function testWorkflowPointCreateValidatorRejectsDuplicateStatusInWorkflow(): void
	{
		$this->expectException(ValidationException::class);

		$repository = $this->app->make(WorkflowPointRepository::class);
		$validator = new WorkflowPointCreateValidator($repository);
		$command = new WorkflowPointCreateCommand($repository);
		$command->setParams([
			'workflow_id' => 1,
			'status' => 'draft',
			'endpoint' => 'archive',
			'action' => 'Archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
		]);

		$validator->validate($command);
	}

	public function testWorkflowPointUpdateValidatorRejectsStepToSelf(): void
	{
		$this->expectException(ValidationException::class);

		$repository = $this->app->make(WorkflowPointRepository::class);
		$validator = new WorkflowPointUpdateValidator($repository);
		$command = new WorkflowPointUpdateCommand($repository);
		$command->setId(1);
		$command->setParams([
			'steps' => [
				['id' => 1, 'type' => 'workflow-point'],
			],
		]);

		$validator->validate($command);
	}

	public function testWorkflowPointGetValidatorRejectsInvalidSort(): void
	{
		$this->expectException(BadRequestException::class);

		$validator = new WorkflowPointGetValidator();
		$command = new WorkflowPointGetCommand(new FakeWorkflowPointRepository());
		$command->setParams([
			'sort' => ['-invalid'],
		]);

		$validator->validate($command);
	}

	public function testWorkflowPointDeleteValidatorDelegatesToFetch(): void
	{
		$repository = new FakeWorkflowPointRepository();
		$validator = new WorkflowPointDeleteValidator($repository);
		$command = new WorkflowPointDeleteCommand($repository, new FakeWorkflowStepRepository());
		$command->setId(2);

		$validator->validate($command);

		$this->assertSame([['fetch', [2, []]]], $repository->calls);
	}

	public function testWorkflowPointFetchValidatorBubblesRepositoryErrors(): void
	{
		$this->expectException(NotFoundException::class);

		$repository = new FakeWorkflowPointRepository();
		$repository->fetchException = new NotFoundException(['Missing workflow point']);
		$validator = new WorkflowPointFetchValidator($repository);
		$command = new WorkflowPointFetchCommand($repository);
		$command->setId(999);

		$validator->validate($command);
	}
}
