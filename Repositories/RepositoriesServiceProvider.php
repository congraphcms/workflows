<?php
/*
 * This file is part of the cookbook/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Workflows\Repositories;

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
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class RepositoriesServiceProvider extends ServiceProvider {

	/**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
	protected $defer = true;

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
		$this->app->singleton('Cookbook\Workflows\Repositories\WorkflowRepository', function($app) {
			return new WorkflowRepository(
				$app['db']->connection()
			);
		});

		$this->app->alias(
			'Cookbook\Workflows\Repositories\WorkflowRepository', 'Cookbook\Contracts\Workflows\WorkflowRepositoryContract'
		);

		$this->app->singleton('Cookbook\Workflows\Repositories\WorkflowPointRepository', function($app) {
			return new WorkflowPointRepository(
				$app['db']->connection()
			);
		});

		$this->app->alias(
			'Cookbook\Workflows\Repositories\WorkflowPointRepository', 'Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract'
		);

		$this->app->singleton('Cookbook\Workflows\Repositories\WorkflowStepRepository', function($app) {
			return new WorkflowStepRepository(
				$app['db']->connection()
			);
		});

		$this->app->alias(
			'Cookbook\Workflows\Repositories\WorkflowStepRepository', 'Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract'
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
			'workflow' => 'Cookbook\Workflows\Repositories\WorkflowRepository',
			'workflow-step' => 'Cookbook\Workflows\Repositories\WorkflowStepRepository'
		];

		$this->app->make('Cookbook\Contracts\Core\ObjectResolverContract')->maps($mappings);
	}
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'Cookbook\Workflows\Repositories\WorkflowRepository',
			'Cookbook\Contracts\Workflows\WorkflowRepositoryContract',
			'Cookbook\Workflows\Repositories\WorkflowStepRepository',
			'Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract'
		];
	}


}