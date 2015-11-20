<?php
/*
 * This file is part of the cookbook/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Workflows\Handlers;

use Illuminate\Support\ServiceProvider;

use Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowCreateHandler;
use Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowUpdateHandler;
use Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowDeleteHandler;
use Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowFetchHandler;
use Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowGetHandler;

use Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointCreateHandler;
use Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointUpdateHandler;
use Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointDeleteHandler;
use Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointFetchHandler;
use Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointGetHandler;

use Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepCreateHandler;
use Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepUpdateHandler;
use Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepDeleteHandler;
use Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepFetchHandler;
use Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepGetHandler;

/**
 * HandlersServiceProvider service provider for handlers
 * 
 * It will register all handlers to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class HandlersServiceProvider extends ServiceProvider {

	/**
	 * The event listener mappings for package.
	 *
	 * @var array
	 */
	protected $listen = [
	];


	/**
	 * Boot
	 * 
	 * @return void
	 */
	public function boot() {
		$this->mapCommandHandlers();
	}


	/**
	 * Register
	 * 
	 * @return void
	 */
	public function register() {
		$this->registerCommandHandlers();
	}

	/**
	 * Maps Command Handlers
	 *
	 * @return void
	 */
	public function mapCommandHandlers() {
		
		$mappings = [
			// Workflows
			'Cookbook\Workflows\Commands\Workflows\WorkflowCreateCommand' => 
				'Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowCreateHandler@handle',
			'Cookbook\Workflows\Commands\Workflows\WorkflowUpdateCommand' => 
				'Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowUpdateHandler@handle',
			'Cookbook\Workflows\Commands\Workflows\WorkflowDeleteCommand' => 
				'Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowDeleteHandler@handle',
			'Cookbook\Workflows\Commands\Workflows\WorkflowFetchCommand' => 
				'Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowFetchHandler@handle',
			'Cookbook\Workflows\Commands\Workflows\WorkflowGetCommand' => 
				'Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowGetHandler@handle',

			// WorkflowPoints
			'Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand' => 
				'Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointCreateHandler@handle',
			'Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand' => 
				'Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointUpdateHandler@handle',
			'Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand' => 
				'Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointDeleteHandler@handle',
			'Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand' => 
				'Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointFetchHandler@handle',
			'Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand' => 
				'Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointGetHandler@handle',


			// WorkflowSteps
			'Cookbook\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand' => 
				'Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepCreateHandler@handle',
			'Cookbook\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand' => 
				'Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepUpdateHandler@handle',
			'Cookbook\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand' => 
				'Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepDeleteHandler@handle',
			'Cookbook\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand' => 
				'Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepFetchHandler@handle',
			'Cookbook\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand' => 
				'Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepGetHandler@handle',

		];

		$this->app->make('Illuminate\Contracts\Bus\Dispatcher')->maps($mappings);
	}

	/**
	 * Registers Command Handlers
	 *
	 * @return void
	 */
	public function registerCommandHandlers() {
		
		// Workflows
		
		$this->app->bind('Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowCreateHandler', function($app){
			return new WorkflowCreateHandler($app->make('Cookbook\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowUpdateHandler', function($app){
			return new WorkflowUpdateHandler($app->make('Cookbook\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowDeleteHandler', function($app){
			return new WorkflowDeleteHandler(
				$app->make('Cookbook\Contracts\Workflows\WorkflowRepositoryContract'),
				$app->make('Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract'),
				$app->make('Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract')
			);
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowFetchHandler', function($app){
			return new WorkflowFetchHandler($app->make('Cookbook\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowGetHandler', function($app){
			return new WorkflowGetHandler($app->make('Cookbook\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		// WorkflowPoints
		
		$this->app->bind('Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointCreateHandler', function($app){
			return new WorkflowPointCreateHandler($app->make('Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointUpdateHandler', function($app){
			return new WorkflowPointUpdateHandler($app->make('Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointDeleteHandler', function($app){
			return new WorkflowPointDeleteHandler(
				$app->make('Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract'),
				$app->make('Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract')
			);
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointFetchHandler', function($app){
			return new WorkflowPointFetchHandler($app->make('Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointGetHandler', function($app){
			return new WorkflowPointGetHandler($app->make('Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});



		// WorkflowSteps
		
		$this->app->bind('Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepCreateHandler', function($app){
			return new WorkflowStepCreateHandler($app->make('Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepUpdateHandler', function($app){
			return new WorkflowStepUpdateHandler($app->make('Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepDeleteHandler', function($app){
			return new WorkflowStepDeleteHandler($app->make('Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepFetchHandler', function($app){
			return new WorkflowStepFetchHandler($app->make('Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepGetHandler', function($app){
			return new WorkflowStepGetHandler($app->make('Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract'));
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
			'Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowCreateHandler',
			'Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowUpdateHandler',
			'Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowDeleteHandler',
			'Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowFetchHandler',
			'Cookbook\Workflows\Handlers\Commands\Workflows\WorkflowGetHandler',

			// WorkflowPoints
			'Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointCreateHandler',
			'Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointUpdateHandler',
			'Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointDeleteHandler',
			'Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointFetchHandler',
			'Cookbook\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointGetHandler',

			// WorkflowSteps
			'Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepCreateHandler',
			'Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepUpdateHandler',
			'Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepDeleteHandler',
			'Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepFetchHandler',
			'Cookbook\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepGetHandler'
		];
	}
}