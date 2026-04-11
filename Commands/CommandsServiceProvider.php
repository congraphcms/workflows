<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Commands;

use Congraph\Contracts\Workflows\WorkflowPointRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowStepRepositoryContract;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

use Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowGetCommand;

use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand;

use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand;

/**
 * CommandsServiceProvider service provider for commands
 * 
 * It will register all commands to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class CommandsServiceProvider extends ServiceProvider implements DeferrableProvider {


	/**
	* Register
	* 
	* @return void
	*/
	public function register() {
		$this->registerCommands();
	}

	/**
	* Register Command
	*
	* @return void
	*/
	public function registerCommands() {
		// Workflows
		
		$this->app->bind(WorkflowCreateCommand::class, function($app){
			return new WorkflowCreateCommand($app->make(WorkflowRepositoryContract::class));
		});

		$this->app->bind(WorkflowUpdateCommand::class, function($app){
			return new WorkflowUpdateCommand($app->make(WorkflowRepositoryContract::class));
		});

		$this->app->bind(WorkflowDeleteCommand::class, function($app){
			return new WorkflowDeleteCommand(
				$app->make(WorkflowRepositoryContract::class),
				$app->make(WorkflowPointRepositoryContract::class),
				$app->make(WorkflowStepRepositoryContract::class)
			);
		});

		$this->app->bind(WorkflowFetchCommand::class, function($app){
			return new WorkflowFetchCommand($app->make(WorkflowRepositoryContract::class));
		});

		$this->app->bind(WorkflowGetCommand::class, function($app){
			return new WorkflowGetCommand($app->make(WorkflowRepositoryContract::class));
		});

		// WorkflowPoints
		
		$this->app->bind(WorkflowPointCreateCommand::class, function($app){
			return new WorkflowPointCreateCommand($app->make(WorkflowPointRepositoryContract::class));
		});

		$this->app->bind(WorkflowPointUpdateCommand::class, function($app){
			return new WorkflowPointUpdateCommand($app->make(WorkflowPointRepositoryContract::class));
		});

		$this->app->bind(WorkflowPointDeleteCommand::class, function($app){
			return new WorkflowPointDeleteCommand(
				$app->make(WorkflowPointRepositoryContract::class),
				$app->make(WorkflowStepRepositoryContract::class)
			);
		});

		$this->app->bind(WorkflowPointFetchCommand::class, function($app){
			return new WorkflowPointFetchCommand($app->make(WorkflowPointRepositoryContract::class));
		});

		$this->app->bind(WorkflowPointGetCommand::class, function($app){
			return new WorkflowPointGetCommand($app->make(WorkflowPointRepositoryContract::class));
		});



		// WorkflowSteps
		
		$this->app->bind(WorkflowStepCreateCommand::class, function($app){
			return new WorkflowStepCreateCommand($app->make(WorkflowStepRepositoryContract::class));
		});

		$this->app->bind(WorkflowStepUpdateCommand::class, function($app){
			return new WorkflowStepUpdateCommand($app->make(WorkflowStepRepositoryContract::class));
		});

		$this->app->bind(WorkflowStepDeleteCommand::class, function($app){
			return new WorkflowStepDeleteCommand($app->make(WorkflowStepRepositoryContract::class));
		});

		$this->app->bind(WorkflowStepFetchCommand::class, function($app){
			return new WorkflowStepFetchCommand($app->make(WorkflowStepRepositoryContract::class));
		});

		$this->app->bind(WorkflowStepGetCommand::class, function($app){
			return new WorkflowStepGetCommand($app->make(WorkflowStepRepositoryContract::class));
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			// Workflows
			WorkflowCreateCommand::class,
			WorkflowUpdateCommand::class,
			WorkflowDeleteCommand::class,
			WorkflowFetchCommand::class,
			WorkflowGetCommand::class,

			// WorkflowPoints
			WorkflowPointCreateCommand::class,
			WorkflowPointUpdateCommand::class,
			WorkflowPointDeleteCommand::class,
			WorkflowPointFetchCommand::class,
			WorkflowPointGetCommand::class,

			// WorkflowSteps
			WorkflowStepCreateCommand::class,
			WorkflowStepUpdateCommand::class,
			WorkflowStepDeleteCommand::class,
			WorkflowStepFetchCommand::class,
			WorkflowStepGetCommand::class,
		];
	}
}
