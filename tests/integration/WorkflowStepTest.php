<?php

require_once __DIR__ . '/../Fixtures/DatabaseTestCase.php';

use Congraph\Core\Exceptions\NotFoundException;
use Congraph\Core\Exceptions\ValidationException;
use Congraph\Core\Repositories\Collection;
use Congraph\Core\Repositories\Model;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand;

final class WorkflowStepTest extends DatabaseTestCase
{
	protected function seeders(): array
	{
		return [
			Database\Seeders\WorkflowTestDbSeeder::class,
			Database\Seeders\WorkflowPointTestDbSeeder::class,
			Database\Seeders\WorkflowStepTestDbSeeder::class,
		];
	}

	protected function cleanupSeeders(): array
	{
		return [
			Database\Seeders\ClearDB::class,
		];
	}

	public function testCreateWorkflowStep(): void
	{
		$command = $this->app->make(WorkflowStepCreateCommand::class);
		$command->setParams([
			'workflow_id' => 1,
			'from_id' => 3,
			'to_id' => 1,
		]);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame(1, $result->workflow_id);
		$this->assertSame(1, $result->workflow->id);
		$this->assertSame(3, $result->from_id);
		$this->assertSame(3, $result->from->id);
		$this->assertSame(1, $result->to_id);
		$this->assertSame(1, $result->to->id);

		$this->assertDatabaseHas('workflow_steps', [
			'id' => 5,
			'workflow_id' => 1,
			'from_id' => 3,
			'to_id' => 1,
		]);
	}

	public function testCreateWorkflowStepThrowsValidationException(): void
	{
		$this->expectException(ValidationException::class);

		$command = $this->app->make(WorkflowStepCreateCommand::class);
		$command->setParams([
			'workflow_id' => 5,
			'from_id' => 3,
			'to_id' => 1,
		]);

		$this->dispatch($command);
	}

	public function testUpdateWorkflowStep(): void
	{
		$command = $this->app->make(WorkflowStepUpdateCommand::class);
		$command->setId(1);
		$command->setParams([
			'to_id' => 3,
		]);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame(1, $result->id);
		$this->assertSame(3, $result->to_id);
		$this->assertSame(1, $result->from_id);
	}

	public function testUpdateWorkflowStepThrowsValidationException(): void
	{
		$this->expectException(ValidationException::class);

		$command = $this->app->make(WorkflowStepUpdateCommand::class);
		$command->setId(1);
		$command->setParams([
			'from_id' => 3,
		]);

		$this->dispatch($command);
	}

	public function testDeleteWorkflowStep(): void
	{
		$command = $this->app->make(WorkflowStepDeleteCommand::class);
		$command->setId(1);

		$result = $this->dispatch($command);

		$this->assertSame(1, $result);
	}

	public function testDeleteWorkflowStepThrowsWhenMissing(): void
	{
		$this->expectException(NotFoundException::class);

		$command = $this->app->make(WorkflowStepDeleteCommand::class);
		$command->setId(133);

		$this->dispatch($command);
	}

	public function testFetchWorkflowStep(): void
	{
		$command = $this->app->make(WorkflowStepFetchCommand::class);
		$command->setId(1);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame(1, $result->id);
		$this->assertSame(1, $result->workflow_id);
		$this->assertSame(1, $result->workflow->id);
		$this->assertSame(1, $result->from_id);
		$this->assertSame(1, $result->from->id);
		$this->assertSame(2, $result->to_id);
		$this->assertSame(2, $result->to->id);
	}

	public function testGetWorkflowSteps(): void
	{
		$command = $this->app->make(WorkflowStepGetCommand::class);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Collection::class, $result);
		$this->assertCount(4, $result);
	}
}
