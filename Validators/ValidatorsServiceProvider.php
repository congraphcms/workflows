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

use Congraph\Contracts\Workflows\WorkflowPointRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowStepRepositoryContract;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand;
use Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand;
use Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowGetCommand;
use Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand;
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
use Illuminate\Contracts\Bus\Dispatcher;

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
			WorkflowCreateCommand::class => WorkflowCreateValidator::class . '@validate',
			WorkflowUpdateCommand::class => WorkflowUpdateValidator::class . '@validate',
			WorkflowDeleteCommand::class => WorkflowDeleteValidator::class . '@validate',
			WorkflowFetchCommand::class => WorkflowFetchValidator::class . '@validate',
			WorkflowGetCommand::class => WorkflowGetValidator::class . '@validate',

			// WorkflowPoints
			WorkflowPointCreateCommand::class => WorkflowPointCreateValidator::class . '@validate',
			WorkflowPointUpdateCommand::class => WorkflowPointUpdateValidator::class . '@validate',
			WorkflowPointDeleteCommand::class => WorkflowPointDeleteValidator::class . '@validate',
			WorkflowPointFetchCommand::class => WorkflowPointFetchValidator::class . '@validate',
			WorkflowPointGetCommand::class => WorkflowPointGetValidator::class . '@validate',

			// WorkflowSteps
			WorkflowStepCreateCommand::class => WorkflowStepCreateValidator::class . '@validate',
			WorkflowStepUpdateCommand::class => WorkflowStepUpdateValidator::class . '@validate',
			WorkflowStepDeleteCommand::class => WorkflowStepDeleteValidator::class . '@validate',
			WorkflowStepFetchCommand::class => WorkflowStepFetchValidator::class . '@validate',
			WorkflowStepGetCommand::class => WorkflowStepGetValidator::class . '@validate',
		];

		$this->app->make(Dispatcher::class)->mapValidators($mappings);
	}

	/**
	 * Registers Command Handlers
	 *
	 * @return void
	 */
	public function registerValidators() {

		// Workflows
		$this->app->bind(WorkflowCreateValidator::class, function($app){
			return new WorkflowCreateValidator();
		});

		$this->app->bind(WorkflowUpdateValidator::class, function($app){
			return new WorkflowUpdateValidator($app[WorkflowRepositoryContract::class]);
		});

		$this->app->bind(WorkflowDeleteValidator::class, function($app){
			return new WorkflowDeleteValidator($app[WorkflowRepositoryContract::class]);
		});

		$this->app->bind(WorkflowFetchValidator::class, function($app){
			return new WorkflowFetchValidator($app[WorkflowRepositoryContract::class]);
		});

		$this->app->bind(WorkflowGetValidator::class, function($app){
			return new WorkflowGetValidator();
		});


		// WorkflowPoints
		$this->app->bind(WorkflowPointCreateValidator::class, function($app){
			return new WorkflowPointCreateValidator($app[WorkflowPointRepositoryContract::class]);
		});

		$this->app->bind(WorkflowPointUpdateValidator::class, function($app){
			return new WorkflowPointUpdateValidator($app[WorkflowPointRepositoryContract::class]);
		});

		$this->app->bind(WorkflowPointDeleteValidator::class, function($app){
			return new WorkflowPointDeleteValidator($app[WorkflowPointRepositoryContract::class]);
		});

		$this->app->bind(WorkflowPointFetchValidator::class, function($app){
			return new WorkflowPointFetchValidator($app[WorkflowPointRepositoryContract::class]);
		});

		$this->app->bind(WorkflowPointGetValidator::class, function($app){
			return new WorkflowPointGetValidator();
		});


		// WorkflowSteps
		$this->app->bind(WorkflowStepCreateValidator::class, function($app){
			return new WorkflowStepCreateValidator(
				$app[WorkflowStepRepositoryContract::class], 
				$app[WorkflowPointRepositoryContract::class]
			);
		});

		$this->app->bind(WorkflowStepUpdateValidator::class, function($app){
			return new WorkflowStepUpdateValidator(
				$app[WorkflowStepRepositoryContract::class], 
				$app[WorkflowPointRepositoryContract::class]
			);
		});

		$this->app->bind(WorkflowStepDeleteValidator::class, function($app){
			return new WorkflowStepDeleteValidator($app[WorkflowStepRepositoryContract::class]);
		});

		$this->app->bind(WorkflowStepFetchValidator::class, function($app){
			return new WorkflowStepFetchValidator($app[WorkflowStepRepositoryContract::class]);
		});

		$this->app->bind(WorkflowStepGetValidator::class, function($app){
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
			WorkflowCreateValidator::class,
			WorkflowUpdateValidator::class,
			WorkflowDeleteValidator::class,
			WorkflowFetchValidator::class,
			WorkflowGetValidator::class,

			// WorkflowPoints
			WorkflowPointCreateValidator::class,
			WorkflowPointUpdateValidator::class,
			WorkflowPointDeleteValidator::class,
			WorkflowPointFetchValidator::class,
			WorkflowPointGetValidator::class,

			// WorkflowSteps
			WorkflowStepCreateValidator::class,
			WorkflowStepUpdateValidator::class,
			WorkflowStepDeleteValidator::class,
			WorkflowStepFetchValidator::class,
			WorkflowStepGetValidator::class,
		];
	}
}
