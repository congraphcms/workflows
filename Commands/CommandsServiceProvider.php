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
class CommandsServiceProvider extends ServiceProvider {

	/**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
	protected $defer = true;


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
		
		$this->app->bind('Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand', function($app){
			return new WorkflowCreateCommand($app->make('Congraph\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand', function($app){
			return new WorkflowUpdateCommand($app->make('Congraph\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand', function($app){
			return new WorkflowDeleteCommand(
				$app->make('Congraph\Contracts\Workflows\WorkflowRepositoryContract'),
				$app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'),
				$app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract')
			);
		});

		$this->app->bind('Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand', function($app){
			return new WorkflowFetchCommand($app->make('Congraph\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Commands\Workflows\WorkflowGetCommand', function($app){
			return new WorkflowGetCommand($app->make('Congraph\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		// WorkflowPoints
		
		$this->app->bind('Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand', function($app){
			return new WorkflowPointCreateCommand($app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand', function($app){
			return new WorkflowPointUpdateCommand($app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand', function($app){
			return new WorkflowPointDeleteCommand(
				$app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'),
				$app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract')
			);
		});

		$this->app->bind('Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand', function($app){
			return new WorkflowPointFetchCommand($app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand', function($app){
			return new WorkflowPointGetCommand($app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});



		// WorkflowSteps
		
		$this->app->bind('Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand', function($app){
			return new WorkflowStepCreateCommand($app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand', function($app){
			return new WorkflowStepUpdateCommand($app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand', function($app){
			return new WorkflowStepDeleteCommand($app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand', function($app){
			return new WorkflowStepFetchCommand($app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand', function($app){
			return new WorkflowStepGetCommand($app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'));
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
			'Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand',
			'Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand',
			'Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand',
			'Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand',
			'Congraph\Workflows\Commands\Workflows\WorkflowGetCommand',

			// WorkflowPoints
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand',

			// WorkflowSteps
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand'
		];
	}
}