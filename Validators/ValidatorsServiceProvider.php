<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Validators;

use Illuminate\Support\ServiceProvider;

use Congraph\Workflows\Validators\Workflows\WorkflowCreateValidator;
use Congraph\Workflows\Validators\Workflows\WorkflowUpdateValidator;
use Congraph\Workflows\Validators\Workflows\WorkflowDeleteValidator;
use Congraph\Workflows\Validators\Workflows\WorkflowFetchValidator;
use Congraph\Workflows\Validators\Workflows\WorkflowGetValidator;

use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointCreateValidator;
use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointUpdateValidator;
use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointDeleteValidator;
use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointFetchValidator;
use Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointGetValidator;

use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepCreateValidator;
use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepUpdateValidator;
use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepDeleteValidator;
use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepFetchValidator;
use Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepGetValidator;

/**
 * ValidatorsServiceProvider service provider for validators
 * 
 * It will register all validators to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
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
			'Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand' => 
				'Congraph\Workflows\Validators\Workflows\WorkflowCreateValidator@validate',
			'Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand' => 
				'Congraph\Workflows\Validators\Workflows\WorkflowUpdateValidator@validate',
			'Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand' => 
				'Congraph\Workflows\Validators\Workflows\WorkflowDeleteValidator@validate',
			'Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand' => 
				'Congraph\Workflows\Validators\Workflows\WorkflowFetchValidator@validate',
			'Congraph\Workflows\Commands\Workflows\WorkflowGetCommand' => 
				'Congraph\Workflows\Validators\Workflows\WorkflowGetValidator@validate',

			// WorkflowPoints
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand' => 
				'Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointCreateValidator@validate',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand' => 
				'Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointUpdateValidator@validate',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand' => 
				'Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointDeleteValidator@validate',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand' => 
				'Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointFetchValidator@validate',
			'Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand' => 
				'Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointGetValidator@validate',

			// WorkflowSteps
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand' => 
				'Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepCreateValidator@validate',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand' => 
				'Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepUpdateValidator@validate',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand' => 
				'Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepDeleteValidator@validate',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand' => 
				'Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepFetchValidator@validate',
			'Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand' => 
				'Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepGetValidator@validate',
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
		$this->app->bind('Congraph\Workflows\Validators\Workflows\WorkflowCreateValidator', function($app){
			return new WorkflowCreateValidator();
		});

		$this->app->bind('Congraph\Workflows\Validators\Workflows\WorkflowUpdateValidator', function($app){
			return new WorkflowUpdateValidator($app['Congraph\Contracts\Workflows\WorkflowRepositoryContract']);
		});

		$this->app->bind('Congraph\Workflows\Validators\Workflows\WorkflowDeleteValidator', function($app){
			return new WorkflowDeleteValidator($app['Congraph\Contracts\Workflows\WorkflowRepositoryContract']);
		});

		$this->app->bind('Congraph\Workflows\Validators\Workflows\WorkflowFetchValidator', function($app){
			return new WorkflowFetchValidator($app['Congraph\Contracts\Workflows\WorkflowRepositoryContract']);
		});

		$this->app->bind('Congraph\Workflows\Validators\Workflows\WorkflowGetValidator', function($app){
			return new WorkflowGetValidator();
		});


		// WorkflowPoints
		$this->app->bind('Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointCreateValidator', function($app){
			return new WorkflowPointCreateValidator($app['Congraph\Contracts\Workflows\WorkflowPointRepositoryContract']);
		});

		$this->app->bind('Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointUpdateValidator', function($app){
			return new WorkflowPointUpdateValidator($app['Congraph\Contracts\Workflows\WorkflowPointRepositoryContract']);
		});

		$this->app->bind('Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointDeleteValidator', function($app){
			return new WorkflowPointDeleteValidator($app['Congraph\Contracts\Workflows\WorkflowPointRepositoryContract']);
		});

		$this->app->bind('Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointFetchValidator', function($app){
			return new WorkflowPointFetchValidator($app['Congraph\Contracts\Workflows\WorkflowPointRepositoryContract']);
		});

		$this->app->bind('Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointGetValidator', function($app){
			return new WorkflowPointGetValidator();
		});


		// WorkflowSteps
		$this->app->bind('Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepCreateValidator', function($app){
			return new WorkflowStepCreateValidator(
				$app['Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'], 
				$app['Congraph\Contracts\Workflows\WorkflowPointRepositoryContract']
			);
		});

		$this->app->bind('Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepUpdateValidator', function($app){
			return new WorkflowStepUpdateValidator(
				$app['Congraph\Contracts\Workflows\WorkflowStepRepositoryContract'], 
				$app['Congraph\Contracts\Workflows\WorkflowPointRepositoryContract']
			);
		});

		$this->app->bind('Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepDeleteValidator', function($app){
			return new WorkflowStepDeleteValidator($app['Congraph\Contracts\Workflows\WorkflowStepRepositoryContract']);
		});

		$this->app->bind('Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepFetchValidator', function($app){
			return new WorkflowStepFetchValidator($app['Congraph\Contracts\Workflows\WorkflowStepRepositoryContract']);
		});

		$this->app->bind('Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepGetValidator', function($app){
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
			'Congraph\Workflows\Validators\Workflows\WorkflowCreateValidator',
			'Congraph\Workflows\Validators\Workflows\WorkflowUpdateValidator',
			'Congraph\Workflows\Validators\Workflows\WorkflowDeleteValidator',
			'Congraph\Workflows\Validators\Workflows\WorkflowFetchValidator',
			'Congraph\Workflows\Validators\Workflows\WorkflowGetValidator',

			// WorkflowPoints
			'Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointCreateValidator',
			'Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointUpdateValidator',
			'Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointDeleteValidator',
			'Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointFetchValidator',
			'Congraph\Workflows\Validators\WorkflowPoints\WorkflowPointGetValidator',

			// WorkflowSteps
			'Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepCreateValidator',
			'Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepUpdateValidator',
			'Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepDeleteValidator',
			'Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepFetchValidator',
			'Congraph\Workflows\Validators\WorkflowSteps\WorkflowStepGetValidator',
		];
	}
}