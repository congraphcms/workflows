<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Repositories;

use Congraph\Contracts\Core\ObjectResolverContract;
use Congraph\Contracts\Workflows\WorkflowPointRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowStepRepositoryContract;
use Illuminate\Support\ServiceProvider;

/**
 * RepositoriesServiceProvider service provider for repositories
 * 
 * It will register all repositories to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class RepositoriesServiceProvider extends ServiceProvider {

	/**
	 * Boot
	 * @return void
	 */
	public function boot()
	{
		$this->mapObjectResolvers();
	}
	
	/**
	 * Register
	 * 
	 * @return void
	 */
	public function register()
	{
		$this->registerRepositories();
	}

	/**
	 * Register repositories to Container
	 *
	 * @return void
	 */
	protected function registerRepositories()
	{
		$this->app->singleton(WorkflowRepository::class, function($app) {
			return new WorkflowRepository(
				$app['db']->connection()
			);
		});

		$this->app->alias(
			WorkflowRepository::class, WorkflowRepositoryContract::class
		);

		$this->app->singleton(WorkflowPointRepository::class, function($app) {
			return new WorkflowPointRepository(
				$app['db']->connection()
			);
		});

		$this->app->alias(
			WorkflowPointRepository::class, WorkflowPointRepositoryContract::class
		);

		$this->app->singleton(WorkflowStepRepository::class, function($app) {
			return new WorkflowStepRepository(
				$app['db']->connection()
			);
		});

		$this->app->alias(
			WorkflowStepRepository::class, WorkflowStepRepositoryContract::class
		);

	}

	/**
	 * Map repositories to object resolver
	 *
	 * @return void
	 */
	protected function mapObjectResolvers()
	{
		$mappings = [
			'workflow' => WorkflowRepository::class,
			'workflow-point' => WorkflowPointRepository::class,
			'workflow-step' => WorkflowStepRepository::class,
		];

		$this->app->make(ObjectResolverContract::class)->maps($mappings);
	}
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			WorkflowRepository::class,
			WorkflowRepositoryContract::class,
			WorkflowPointRepository::class,
			WorkflowPointRepositoryContract::class,
			WorkflowStepRepository::class,
			WorkflowStepRepositoryContract::class,
		];
	}


}
