<?php
/*
 * This file is part of the cookbook/locales package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Locales\Validators;

use Illuminate\Support\ServiceProvider;

use Cookbook\Locales\Validators\Locales\LocaleCreateValidator;
use Cookbook\Locales\Validators\Locales\LocaleUpdateValidator;
use Cookbook\Locales\Validators\Locales\LocaleDeleteValidator;
use Cookbook\Locales\Validators\Locales\LocaleFetchValidator;
use Cookbook\Locales\Validators\Locales\LocaleGetValidator;

/**
 * ValidatorsServiceProvider service provider for validators
 * 
 * It will register all validators to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/locales
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
			// Locales
			'Cookbook\Locales\Commands\Locales\LocaleCreateCommand' => 
				'Cookbook\Locales\Validators\Locales\LocaleCreateValidator@validate',
			'Cookbook\Locales\Commands\Locales\LocaleUpdateCommand' => 
				'Cookbook\Locales\Validators\Locales\LocaleUpdateValidator@validate',
			'Cookbook\Locales\Commands\Locales\LocaleDeleteCommand' => 
				'Cookbook\Locales\Validators\Locales\LocaleDeleteValidator@validate',
			'Cookbook\Locales\Commands\Locales\LocaleFetchCommand' => 
				'Cookbook\Locales\Validators\Locales\LocaleFetchValidator@validate',
			'Cookbook\Locales\Commands\Locales\LocaleGetCommand' => 
				'Cookbook\Locales\Validators\Locales\LocaleGetValidator@validate',
		];

		$this->app->make('Illuminate\Contracts\Bus\Dispatcher')->mapValidators($mappings);
	}

	/**
	 * Registers Command Handlers
	 *
	 * @return void
	 */
	public function registerValidators() {

		// Locales
		$this->app->bind('Cookbook\Locales\Validators\Locales\LocaleCreateValidator', function($app){
			return new LocaleCreateValidator();
		});

		$this->app->bind('Cookbook\Locales\Validators\Locales\LocaleUpdateValidator', function($app){
			return new LocaleUpdateValidator($app['Cookbook\Contracts\Locales\LocaleRepositoryContract']);
		});

		$this->app->bind('Cookbook\Locales\Validators\Locales\LocaleDeleteValidator', function($app){
			return new LocaleDeleteValidator($app['Cookbook\Contracts\Locales\LocaleRepositoryContract']);
		});

		$this->app->bind('Cookbook\Locales\Validators\Locales\LocaleFetchValidator', function($app){
			return new LocaleFetchValidator($app['Cookbook\Contracts\Locales\LocaleRepositoryContract']);
		});

		$this->app->bind('Cookbook\Locales\Validators\Locales\LocaleGetValidator', function($app){
			return new LocaleGetValidator();
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
			// Locales
			'Cookbook\Locales\Validators\Locales\LocaleCreateValidator',
			'Cookbook\Locales\Validators\Locales\LocaleUpdateValidator',
			'Cookbook\Locales\Validators\Locales\LocaleDeleteValidator',
			'Cookbook\Locales\Validators\Locales\LocaleFetchValidator',
			'Cookbook\Locales\Validators\Locales\LocaleGetValidator'
		];
	}
}