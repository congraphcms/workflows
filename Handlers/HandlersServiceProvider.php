<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Handlers;

use Illuminate\Support\ServiceProvider;

use Congraph\Workflows\Handlers\Commands\Workflows\WorkflowCreateHandler;
use Congraph\Workflows\Handlers\Commands\Workflows\WorkflowUpdateHandler;
use Congraph\Workflows\Handlers\Commands\Workflows\WorkflowDeleteHandler;
use Congraph\Workflows\Handlers\Commands\Workflows\WorkflowFetchHandler;
use Congraph\Workflows\Handlers\Commands\Workflows\WorkflowGetHandler;

use Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointCreateHandler;
use Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointUpdateHandler;
use Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointDeleteHandler;
use Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointFetchHandler;
use Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointGetHandler;

use Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepCreateHandler;
use Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepUpdateHandler;
use Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepDeleteHandler;
use Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepFetchHandler;
use Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepGetHandler;

/**
 * HandlersServiceProvider service provider for handlers
 * 
 * It will register all handlers to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
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
			'Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand' => 
				'Congraph\Workflows\Handlers\Commands\Workflows\WorkflowCreateHandler@handle',
			'Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand' => 
				'Congraph\Workflows\Handlers\Commands\Workflows\WorkflowUpdateHandler@handle',
			'Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand' => 
				'Congraph\Workflows\Handlers\Commands\Workflows\WorkflowDeleteHandler@handle',
			'Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand' => 
				'Congraph\Workflows\Handlers\Commands\Workflows\WorkflowFetchHandler@handle',
			'Congraph\Workflows\Commands\Workflows\WorkflowGetCommand' => 
				'Congraph\Workflows\Handlers\Commands\Workflows\WorkflowGetHandler@handle',

			// WorkflowPoints
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand' => 
				'Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointCreateHandler@handle',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand' => 
				'Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointUpdateHandler@handle',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand' => 
				'Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointDeleteHandler@handle',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand' => 
				'Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointFetchHandler@handle',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand' => 
				'Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointGetHandler@handle',


			// WorkflowSteps
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand' => 
				'Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepCreateHandler@handle',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand' => 
				'Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepUpdateHandler@handle',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand' => 
				'Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepDeleteHandler@handle',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand' => 
				'Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepFetchHandler@handle',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand' => 
				'Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepGetHandler@handle',

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
		
		$this->app->bind('Congraph\Workflows\Handlers\Commands\Workflows\WorkflowCreateHandler', function($app){
			return new WorkflowCreateHandler($app->make('Congraph\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\Workflows\WorkflowUpdateHandler', function($app){
			return new WorkflowUpdateHandler($app->make('Congraph\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\Workflows\WorkflowDeleteHandler', function($app){
			return new WorkflowDeleteHandler(
				$app->make('Congraph\Contracts\Workflows\WorkflowRepositoryContract'),
				$app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'),
				$app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract')
			);
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\Workflows\WorkflowFetchHandler', function($app){
			return new WorkflowFetchHandler($app->make('Congraph\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\Workflows\WorkflowGetHandler', function($app){
			return new WorkflowGetHandler($app->make('Congraph\Contracts\Workflows\WorkflowRepositoryContract'));
		});

		// WorkflowPoints
		
		$this->app->bind('Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointCreateHandler', function($app){
			return new WorkflowPointCreateHandler($app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointUpdateHandler', function($app){
			return new WorkflowPointUpdateHandler($app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointDeleteHandler', function($app){
			return new WorkflowPointDeleteHandler(
				$app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'),
				$app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract')
			);
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointFetchHandler', function($app){
			return new WorkflowPointFetchHandler($app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointGetHandler', function($app){
			return new WorkflowPointGetHandler($app->make('Congraph\Contracts\Workflows\WorkflowPointRepositoryContract'));
		});



		// WorkflowSteps
		
		$this->app->bind('Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepCreateHandler', function($app){
			return new WorkflowStepCreateHandler($app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepUpdateHandler', function($app){
			return new WorkflowStepUpdateHandler($app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepDeleteHandler', function($app){
			return new WorkflowStepDeleteHandler($app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepFetchHandler', function($app){
			return new WorkflowStepFetchHandler($app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'));
		});

		$this->app->bind('Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepGetHandler', function($app){
			return new WorkflowStepGetHandler($app->make('Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'));
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
			'Congraph\Workflows\Handlers\Commands\Workflows\WorkflowCreateHandler',
			'Congraph\Workflows\Handlers\Commands\Workflows\WorkflowUpdateHandler',
			'Congraph\Workflows\Handlers\Commands\Workflows\WorkflowDeleteHandler',
			'Congraph\Workflows\Handlers\Commands\Workflows\WorkflowFetchHandler',
			'Congraph\Workflows\Handlers\Commands\Workflows\WorkflowGetHandler',

			// WorkflowPoints
			'Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointCreateHandler',
			'Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointUpdateHandler',
			'Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointDeleteHandler',
			'Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointFetchHandler',
			'Congraph\Workflows\Handlers\Commands\WorkflowPoints\WorkflowPointGetHandler',

			// WorkflowSteps
			'Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepCreateHandler',
			'Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepUpdateHandler',
			'Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepDeleteHandler',
			'Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepFetchHandler',
			'Congraph\Workflows\Handlers\Commands\WorkflowSteps\WorkflowStepGetHandler'
		];
	}
}