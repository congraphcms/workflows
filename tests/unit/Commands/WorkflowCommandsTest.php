<?php

require_once __DIR__ . '/../../Fixtures/Commands/FakeWorkflowRepositories.php';

use Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowGetCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand;

final class WorkflowCommandsTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateCommandDelegatesToRepositoryCreate(): void
	{
		$repository = new FakeWorkflowRepository();
		$repository->createResult = (object) ['id' => 1, 'name' => 'Basic Publishing'];

		$command = new WorkflowCreateCommand($repository);
		$command->setParams(['name' => 'Basic Publishing']);

		$result = $command->handle();

		$this->assertSame($repository->createResult, $result);
		$this->assertSame([['create', [['name' => 'Basic Publishing']]]], $repository->calls);
	}

	public function testUpdateCommandDelegatesToRepositoryUpdate(): void
	{
		$repository = new FakeWorkflowRepository();
		$repository->updateResult = (object) ['id' => 1, 'name' => 'Changed'];

		$command = new WorkflowUpdateCommand($repository);
		$command->setId(1);
		$command->setParams(['name' => 'Changed']);

		$result = $command->handle();

		$this->assertSame($repository->updateResult, $result);
		$this->assertSame([['update', [1, ['name' => 'Changed']]]], $repository->calls);
	}

	public function testDeleteCommandDeletesWorkflowAndRelatedPoints(): void
	{
		$repository = new FakeWorkflowRepository();
		$pointRepository = new FakeWorkflowPointRepository();
		$stepRepository = new FakeWorkflowStepRepository();
		$repository->deleteResult = (object) ['id' => 5];

		$command = new WorkflowDeleteCommand($repository, $pointRepository, $stepRepository);
		$command->setId(5);

		$result = $command->handle();

		$this->assertSame(5, $result);
		$this->assertSame([['delete', [5]]], $repository->calls);
		$this->assertSame([['deleteByWorkflow', [5]]], $pointRepository->calls);
		$this->assertSame([], $stepRepository->calls);
	}

	public function testFetchCommandPassesIncludeToRepository(): void
	{
		$repository = new FakeWorkflowRepository();
		$repository->fetchResult = (object) ['id' => 1];

		$command = new WorkflowFetchCommand($repository);
		$command->setId(1);
		$command->setParams(['include' => ['points']]);

		$result = $command->handle();

		$this->assertSame($repository->fetchResult, $result);
		$this->assertSame([['fetch', [1, ['points']]]], $repository->calls);
	}

	public function testGetCommandPassesDefaultsToRepository(): void
	{
		$repository = new FakeWorkflowRepository();
		$repository->getResult = [(object) ['id' => 1]];

		$command = new WorkflowGetCommand($repository);
		$command->setParams([
			'filter' => ['name' => 'Basic Publishing'],
			'offset' => 5,
			'limit' => 10,
			'sort' => ['-created_at'],
			'include' => ['points'],
		]);

		$result = $command->handle();

		$this->assertSame($repository->getResult, $result);
		$this->assertSame([
			['get', [['name' => 'Basic Publishing'], 5, 10, ['-created_at'], ['points']]],
		], $repository->calls);
	}
}
