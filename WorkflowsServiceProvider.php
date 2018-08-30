<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows;

use Illuminate\Support\ServiceProvider;

/**
 * WorkflowsServiceProvider service provider for Workflows package
 * 
 * It will register all dependecies to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowsServiceProvider extends ServiceProvider {

	/**
	* Register
	* 
	* @return void
	*/
	public function register() {
		// $this->mergeConfigFrom(realpath(__DIR__ . '/config/congraph.php'), 'congraph');
		$this->registerServiceProviders();
	}

	/**
	 * Boot
	 * 
	 * @return void
	 */
	public function boot() {
		$this->publishes([
			__DIR__.'/database' => database_path(''),
		]);
	}

	/**
	 * Register Service Providers for this package
	 * 
	 * @return void
	 */
	protected function registerServiceProviders(){

		// Repositories
		// -----------------------------------------------------------------------------
		$this->app->register('Congraph\Workflows\Repositories\RepositoriesServiceProvider');
		
		// Handlers
		// -----------------------------------------------------------------------------
		$this->app->register('Congraph\Workflows\Handlers\HandlersServiceProvider');

		// Validators
		// -----------------------------------------------------------------------------
		$this->app->register('Congraph\Workflows\Validators\ValidatorsServiceProvider');

		// Commands
		// -----------------------------------------------------------------------------
		$this->app->register('Congraph\Workflows\Commands\CommandsServiceProvider');

	}

}