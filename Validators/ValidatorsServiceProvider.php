<?php
/*
 * This file is part of the cookbook/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Workflows\Validators;

use Illuminate\Support\ServiceProvider;

use Cookbook\Workflows\Validators\Workflows\WorkflowCreateValidator;
use Cookbook\Workflows\Validators\Workflows\WorkflowUpdateValidator;
use Cookbook\Workflows\Validators\Workflows\WorkflowDeleteValidator;
use Cookbook\Workflows\Validators\Workflows\WorkflowFetchValidator;
use Cookbook\Workflows\Validators\Workflows\WorkflowGetValidator;

use Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointCreateValidator;
use Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointUpdateValidator;
use Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointDeleteValidator;
use Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointFetchValidator;
use Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointGetValidator;

use Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepCreateValidator;
use Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepUpdateValidator;
use Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepDeleteValidator;
use Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepFetchValidator;
use Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepGetValidator;

/**
 * ValidatorsServiceProvider service provider for validators
 * 
 * It will register all validators to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class ValidatorsServiceProvider extends ServiceProvider {

	/**
	 * Boot
	 * 
	 * @return void
	 */
	public function boot() {
		$this->mapValidators();
	}


	/**
	 * Register
	 * 
	 * @return void
	 */
	public function register() {
		$this->registerValidators();
	}

	/**
	 * Maps Validators
	 *
	 * @return void
	 */
	public function mapValidators() {
		
		$mappings = [
			// Workflows
			'Cookbook\Workflows\Commands\Workflows\WorkflowCreateCommand' => 
				'Cookbook\Workflows\Validators\Workflows\WorkflowCreateValidator@validate',
			'Cookbook\Workflows\Commands\Workflows\WorkflowUpdateCommand' => 
				'Cookbook\Workflows\Validators\Workflows\WorkflowUpdateValidator@validate',
			'Cookbook\Workflows\Commands\Workflows\WorkflowDeleteCommand' => 
				'Cookbook\Workflows\Validators\Workflows\WorkflowDeleteValidator@validate',
			'Cookbook\Workflows\Commands\Workflows\WorkflowFetchCommand' => 
				'Cookbook\Workflows\Validators\Workflows\WorkflowFetchValidator@validate',
			'Cookbook\Workflows\Commands\Workflows\WorkflowGetCommand' => 
				'Cookbook\Workflows\Validators\Workflows\WorkflowGetValidator@validate',

			// WorkflowPoints
			'Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand' => 
				'Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointCreateValidator@validate',
			'Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand' => 
				'Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointUpdateValidator@validate',
			'Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand' => 
				'Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointDeleteValidator@validate',
			'Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand' => 
				'Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointFetchValidator@validate',
			'Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand' => 
				'Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointGetValidator@validate',

			// WorkflowSteps
			'Cookbook\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand' => 
				'Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepCreateValidator@validate',
			'Cookbook\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand' => 
				'Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepUpdateValidator@validate',
			'Cookbook\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand' => 
				'Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepDeleteValidator@validate',
			'Cookbook\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand' => 
				'Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepFetchValidator@validate',
			'Cookbook\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand' => 
				'Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepGetValidator@validate',
		];

		$this->app->make('Illuminate\Contracts\Bus\Dispatcher')->mapValidators($mappings);
	}

	/**
	 * Registers Command Handlers
	 *
	 * @return void
	 */
	public function registerValidators() {

		// Workflows
		$this->app->bind('Cookbook\Workflows\Validators\Workflows\WorkflowCreateValidator', function($app){
			return new WorkflowCreateValidator();
		});

		$this->app->bind('Cookbook\Workflows\Validators\Workflows\WorkflowUpdateValidator', function($app){
			return new WorkflowUpdateValidator($app['Cookbook\Contracts\Workflows\WorkflowRepositoryContract']);
		});

		$this->app->bind('Cookbook\Workflows\Validators\Workflows\WorkflowDeleteValidator', function($app){
			return new WorkflowDeleteValidator($app['Cookbook\Contracts\Workflows\WorkflowRepositoryContract']);
		});

		$this->app->bind('Cookbook\Workflows\Validators\Workflows\WorkflowFetchValidator', function($app){
			return new WorkflowFetchValidator($app['Cookbook\Contracts\Workflows\WorkflowRepositoryContract']);
		});

		$this->app->bind('Cookbook\Workflows\Validators\Workflows\WorkflowGetValidator', function($app){
			return new WorkflowGetValidator();
		});


		// WorkflowPoints
		$this->app->bind('Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointCreateValidator', function($app){
			return new WorkflowPointCreateValidator($app['Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract']);
		});

		$this->app->bind('Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointUpdateValidator', function($app){
			return new WorkflowPointUpdateValidator($app['Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract']);
		});

		$this->app->bind('Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointDeleteValidator', function($app){
			return new WorkflowPointDeleteValidator($app['Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract']);
		});

		$this->app->bind('Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointFetchValidator', function($app){
			return new WorkflowPointFetchValidator($app['Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract']);
		});

		$this->app->bind('Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointGetValidator', function($app){
			return new WorkflowPointGetValidator();
		});


		// WorkflowSteps
		$this->app->bind('Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepCreateValidator', function($app){
			return new WorkflowStepCreateValidator(
				$app['Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract'], 
				$app['Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract']
			);
		});

		$this->app->bind('Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepUpdateValidator', function($app){
			return new WorkflowStepUpdateValidator(
				$app['Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract'], 
				$app['Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract']
			);
		});

		$this->app->bind('Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepDeleteValidator', function($app){
			return new WorkflowStepDeleteValidator($app['Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract']);
		});

		$this->app->bind('Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepFetchValidator', function($app){
			return new WorkflowStepFetchValidator($app['Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract']);
		});

		$this->app->bind('Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepGetValidator', function($app){
			return new WorkflowStepGetValidator();
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
			'Cookbook\Workflows\Validators\Workflows\WorkflowCreateValidator',
			'Cookbook\Workflows\Validators\Workflows\WorkflowUpdateValidator',
			'Cookbook\Workflows\Validators\Workflows\WorkflowDeleteValidator',
			'Cookbook\Workflows\Validators\Workflows\WorkflowFetchValidator',
			'Cookbook\Workflows\Validators\Workflows\WorkflowGetValidator',

			// WorkflowPoints
			'Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointCreateValidator',
			'Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointUpdateValidator',
			'Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointDeleteValidator',
			'Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointFetchValidator',
			'Cookbook\Workflows\Validators\WorkflowPoints\WorkflowPointGetValidator',

			// WorkflowSteps
			'Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepCreateValidator',
			'Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepUpdateValidator',
			'Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepDeleteValidator',
			'Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepFetchValidator',
			'Cookbook\Workflows\Validators\WorkflowSteps\WorkflowStepGetValidator',
		];
	}
}