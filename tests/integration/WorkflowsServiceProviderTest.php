<?php

require_once __DIR__ . '/../Fixtures/DatabaseTestCase.php';

use Congraph\Contracts\Workflows\WorkflowPointRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowStepRepositoryContract;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand;
use Congraph\Workflows\Repositories\WorkflowPointRepository;
use Congraph\Workflows\Repositories\WorkflowRepository;
use Congraph\Workflows\Repositories\WorkflowStepRepository;
use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointCreateValidator;
use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepCreateValidator;
use Congraph\Workflows\Validators\Workflows\WorkflowCreateValidator;

final class WorkflowsServiceProviderTest extends DatabaseTestCase
{
	public function testImportantBindingsResolveFromContainer(): void
	{
		$this->assertInstanceOf(WorkflowRepository::class, $this->app->make(WorkflowRepository::class));
		$this->assertInstanceOf(WorkflowRepositoryContract::class, $this->app->make(WorkflowRepositoryContract::class));
		$this->assertInstanceOf(WorkflowPointRepository::class, $this->app->make(WorkflowPointRepository::class));
		$this->assertInstanceOf(WorkflowPointRepositoryContract::class, $this->app->make(WorkflowPointRepositoryContract::class));
		$this->assertInstanceOf(WorkflowStepRepository::class, $this->app->make(WorkflowStepRepository::class));
		$this->assertInstanceOf(WorkflowStepRepositoryContract::class, $this->app->make(WorkflowStepRepositoryContract::class));
		$this->assertInstanceOf(WorkflowCreateCommand::class, $this->app->make(WorkflowCreateCommand::class));
		$this->assertInstanceOf(WorkflowPointCreateCommand::class, $this->app->make(WorkflowPointCreateCommand::class));
		$this->assertInstanceOf(WorkflowStepCreateCommand::class, $this->app->make(WorkflowStepCreateCommand::class));
		$this->assertInstanceOf(WorkflowCreateValidator::class, $this->app->make(WorkflowCreateValidator::class));
		$this->assertInstanceOf(WorkflowPointCreateValidator::class, $this->app->make(WorkflowPointCreateValidator::class));
		$this->assertInstanceOf(WorkflowStepCreateValidator::class, $this->app->make(WorkflowStepCreateValidator::class));
	}
}
