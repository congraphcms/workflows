<?php

require_once __DIR__ . '/../Fixtures/DatabaseTestCase.php';

use Congraph\Core\Exceptions\NotFoundException;
use Congraph\Core\Exceptions\ValidationException;
use Congraph\Core\Repositories\Collection;
use Congraph\Core\Repositories\Model;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand;

final class WorkflowPointTest extends DatabaseTestCase
{
	protected function seeders(): array
	{
		return [
			Database\Seeders\WorkflowTestDbSeeder::class,
			Database\Seeders\WorkflowPointTestDbSeeder::class,
		];
	}

	protected function cleanupSeeders(): array
	{
		return [
			Database\Seeders\ClearDB::class,
		];
	}

	public function testCreateWorkflowPoint(): void
	{
		$command = $this->app->make(WorkflowPointCreateCommand::class);
		$command->setParams([
			'workflow_id' => 1,
			'status' => 'archived',
			'endpoint' => 'archive',
			'action' => 'Archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'public' => true,
			'deleted' => false,
			'sort_order' => 4,
		]);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame('archived', $result->status);
		$this->assertSame('archive', $result->endpoint);
		$this->assertSame('Archive', $result->action);
		$this->assertSame('Archived', $result->name);
		$this->assertSame('Archived objects', $result->description);
		$this->assertTrue((bool) $result->public);
		$this->assertFalse((bool) $result->deleted);
		$this->assertSame(4, $result->sort_order);

		$this->assertDatabaseHas('workflow_points', [
			'id' => 4,
			'status' => 'archived',
			'endpoint' => 'archive',
			'action' => 'Archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'public' => 1,
			'deleted' => 0,
			'sort_order' => 4,
		]);
	}

	public function testCreateWorkflowPointWithSteps(): void
	{
		$command = $this->app->make(WorkflowPointCreateCommand::class);
		$command->setParams([
			'workflow_id' => 1,
			'status' => 'archived',
			'endpoint' => 'archive',
			'action' => 'Archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'public' => true,
			'deleted' => false,
			'sort_order' => 4,
			'steps' => [
				['id' => 1, 'type' => 'workflow-point'],
				['id' => 2, 'type' => 'workflow-point'],
				['id' => 3, 'type' => 'workflow-point'],
			],
		]);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertCount(3, $result->steps);
		$this->assertDatabaseHas('workflow_steps', [
			'workflow_id' => 1,
			'from_id' => 4,
			'to_id' => 1,
		]);
		$this->assertDatabaseHas('workflow_steps', [
			'workflow_id' => 1,
			'from_id' => 4,
			'to_id' => 2,
		]);
		$this->assertDatabaseHas('workflow_steps', [
			'workflow_id' => 1,
			'from_id' => 4,
			'to_id' => 3,
		]);
	}

	public function testCreateWorkflowPointThrowsValidationException(): void
	{
		$this->expectException(ValidationException::class);

		$command = $this->app->make(WorkflowPointCreateCommand::class);
		$command->setParams([
			'status' => '',
			'action' => 'archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'sort_order' => 4,
		]);

		$this->dispatch($command);
	}

	public function testUpdateWorkflowPoint(): void
	{
		$command = $this->app->make(WorkflowPointUpdateCommand::class);
		$command->setId(1);
		$command->setParams([
			'status' => 'deleted',
			'action' => 'delete',
		]);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame(1, $result->id);
		$this->assertSame('deleted', $result->status);
		$this->assertSame('delete', $result->action);
	}

	public function testUpdateWorkflowPointWithSteps(): void
	{
		$command = $this->app->make(WorkflowPointUpdateCommand::class);
		$command->setId(1);
		$command->setParams([
			'status' => 'deleted',
			'action' => 'delete',
			'steps' => [
				['id' => 2, 'type' => 'workflow-point'],
				['id' => 3, 'type' => 'workflow-point'],
			],
		]);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame(1, $result->id);
		$this->assertSame('deleted', $result->status);
		$this->assertSame('delete', $result->action);
	}

	public function testUpdateWorkflowPointWithStepToSelfThrowsValidationException(): void
	{
		$this->expectException(ValidationException::class);

		$command = $this->app->make(WorkflowPointUpdateCommand::class);
		$command->setId(1);
		$command->setParams([
			'status' => 'deleted',
			'action' => 'delete',
			'steps' => [
				['id' => 1, 'type' => 'workflow-point'],
				['id' => 2, 'type' => 'workflow-point'],
				['id' => 3, 'type' => 'workflow-point'],
			],
		]);

		$this->dispatch($command);
	}

	public function testUpdateWorkflowPointThrowsValidationException(): void
	{
		$this->expectException(ValidationException::class);

		$command = $this->app->make(WorkflowPointUpdateCommand::class);
		$command->setId(1);
		$command->setParams([
			'status' => 'published',
		]);

		$this->dispatch($command);
	}

	public function testDeleteWorkflowPoint(): void
	{
		$command = $this->app->make(WorkflowPointDeleteCommand::class);
		$command->setId(1);

		$result = $this->dispatch($command);

		$this->assertSame(1, $result);
	}

	public function testDeleteWorkflowPointThrowsWhenMissing(): void
	{
		$this->expectException(NotFoundException::class);

		$command = $this->app->make(WorkflowPointDeleteCommand::class);
		$command->setId(133);

		$this->dispatch($command);
	}

	public function testFetchWorkflowPoint(): void
	{
		$command = $this->app->make(WorkflowPointFetchCommand::class);
		$command->setId(1);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame(1, $result->id);
		$this->assertSame('trashed', $result->status);
	}

	public function testGetWorkflowPoints(): void
	{
		$command = $this->app->make(WorkflowPointGetCommand::class);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Collection::class, $result);
		$this->assertCount(3, $result);
	}
}
