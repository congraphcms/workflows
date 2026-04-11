<?php

require_once __DIR__ . '/../../Fixtures/Validators/ValidatorTestCase.php';
require_once __DIR__ . '/../../Fixtures/Commands/FakeWorkflowRepositories.php';

use Congraph\Core\Exceptions\BadRequestException;
use Congraph\Core\Exceptions\NotFoundException;
use Congraph\Core\Exceptions\ValidationException;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand;
use Congraph\Workflows\Repositories\WorkflowPointRepository;
use Congraph\Workflows\Repositories\WorkflowStepRepository;
use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepCreateValidator;
use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepDeleteValidator;
use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepFetchValidator;
use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepGetValidator;
use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepUpdateValidator;

final class WorkflowStepValidatorsTest extends ValidatorTestCase
{
	protected function seeders(): array
	{
		return [
			Database\Seeders\WorkflowTestDbSeeder::class,
			Database\Seeders\WorkflowPointTestDbSeeder::class,
			Database\Seeders\WorkflowStepTestDbSeeder::class,
		];
	}

	public function testWorkflowStepCreateValidatorRejectsDuplicateStep(): void
	{
		$this->expectException(ValidationException::class);

		$validator = new WorkflowStepCreateValidator(
			$this->app->make(WorkflowStepRepository::class),
			$this->app->make(WorkflowPointRepository::class)
		);
		$command = new WorkflowStepCreateCommand($this->app->make(WorkflowStepRepository::class));
		$command->setParams([
			'workflow_id' => 1,
			'from_id' => 1,
			'to_id' => 2,
		]);

		$validator->validate($command);
	}

	public function testWorkflowStepUpdateValidatorRejectsDuplicateStep(): void
	{
		$this->expectException(ValidationException::class);

		$validator = new WorkflowStepUpdateValidator(
			$this->app->make(WorkflowStepRepository::class),
			$this->app->make(WorkflowPointRepository::class)
		);
		$command = new WorkflowStepUpdateCommand($this->app->make(WorkflowStepRepository::class));
		$command->setId(1);
		$command->setParams([
			'from_id' => 2,
		]);

		$validator->validate($command);
	}

	public function testWorkflowStepGetValidatorRejectsInvalidFilter(): void
	{
		$this->expectException(BadRequestException::class);

		$validator = new WorkflowStepGetValidator();
		$command = new WorkflowStepGetCommand(new FakeWorkflowStepRepository());
		$command->setParams([
			'filter' => ['invalid' => 'value'],
		]);

		$validator->validate($command);
	}

	public function testWorkflowStepDeleteValidatorDelegatesToFetch(): void
	{
		$repository = new FakeWorkflowStepRepository();
		$validator = new WorkflowStepDeleteValidator($repository);
		$command = new WorkflowStepDeleteCommand($repository);
		$command->setId(3);

		$validator->validate($command);

		$this->assertSame([['fetch', [3, []]]], $repository->calls);
	}

	public function testWorkflowStepFetchValidatorBubblesRepositoryErrors(): void
	{
		$this->expectException(NotFoundException::class);

		$repository = new FakeWorkflowStepRepository();
		$repository->fetchException = new NotFoundException(['Missing workflow step']);
		$validator = new WorkflowStepFetchValidator($repository);
		$command = new WorkflowStepFetchCommand($repository);
		$command->setId(999);

		$validator->validate($command);
	}
}
