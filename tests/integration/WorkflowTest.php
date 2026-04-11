<?php

require_once __DIR__ . '/../Fixtures/DatabaseTestCase.php';

use Congraph\Core\Exceptions\NotFoundException;
use Congraph\Core\Exceptions\ValidationException;
use Congraph\Core\Repositories\Collection;
use Congraph\Core\Repositories\Model;
use Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowGetCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand;

final class WorkflowTest extends DatabaseTestCase
{
	protected function seeders(): array
	{
		return [
			Database\Seeders\WorkflowTestDbSeeder::class,
		];
	}

	protected function cleanupSeeders(): array
	{
		return [
			Database\Seeders\ClearDB::class,
		];
	}

	public function testCreateWorkflow(): void
	{
		$command = $this->app->make(WorkflowCreateCommand::class);
		$command->setParams([
			'name' => 'Test workflow',
			'description' => 'Just another description',
		]);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame('Test workflow', $result->name);
		$this->assertSame('Just another description', $result->description);

		$this->assertDatabaseHas('workflows', [
			'id' => 3,
			'name' => 'Test workflow',
			'description' => 'Just another description',
		]);
	}

	public function testCreateWorkflowThrowsValidationException(): void
	{
		$this->expectException(ValidationException::class);

		$command = $this->app->make(WorkflowCreateCommand::class);
		$command->setParams([
			'name' => '',
		]);

		$this->dispatch($command);
	}

	public function testUpdateWorkflow(): void
	{
		$command = $this->app->make(WorkflowUpdateCommand::class);
		$command->setId(1);
		$command->setParams([
			'name' => 'Changed Name',
			'description' => '',
		]);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame(1, $result->id);
		$this->assertSame('Changed Name', $result->name);
		$this->assertSame('', $result->description);
	}

	public function testUpdateWorkflowThrowsValidationException(): void
	{
		$this->expectException(ValidationException::class);

		$command = $this->app->make(WorkflowUpdateCommand::class);
		$command->setId(1);
		$command->setParams([
			'name' => '',
		]);

		$this->dispatch($command);
	}

	public function testDeleteWorkflow(): void
	{
		$command = $this->app->make(WorkflowDeleteCommand::class);
		$command->setId(1);

		$result = $this->dispatch($command);

		$this->assertSame(1, $result);
	}

	public function testDeleteWorkflowThrowsWhenMissing(): void
	{
		$this->expectException(NotFoundException::class);

		$command = $this->app->make(WorkflowDeleteCommand::class);
		$command->setId(133);

		$this->dispatch($command);
	}

	public function testFetchWorkflow(): void
	{
		$command = $this->app->make(WorkflowFetchCommand::class);
		$command->setId(1);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame('Basic Publishing', $result->name);
	}

	public function testGetWorkflows(): void
	{
		$command = $this->app->make(WorkflowGetCommand::class);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Collection::class, $result);
		$this->assertCount(2, $result);
	}
}
