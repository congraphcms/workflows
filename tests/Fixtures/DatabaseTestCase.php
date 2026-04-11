<?php

require_once __DIR__ . '/../database/seeders/WorkflowTestDbSeeder.php';
require_once __DIR__ . '/../database/seeders/WorkflowPointTestDbSeeder.php';
require_once __DIR__ . '/../database/seeders/WorkflowStepTestDbSeeder.php';
require_once __DIR__ . '/../database/seeders/ClearDB.php';

use Congraph\Core\Bus\CommandDispatcher;

abstract class DatabaseTestCase extends \Orchestra\Testbench\TestCase
{
	protected function getPackageProviders($app)
	{
		return [
			\Congraph\Workflows\WorkflowsServiceProvider::class,
			\Congraph\Core\CoreServiceProvider::class,
		];
	}

	protected function defineEnvironment($app)
	{
		$driver = env('DB_CONNECTION', 'sqlite');

		$connection = [
			'driver' => $driver,
			'prefix' => '',
		];

		if ($driver === 'sqlite') {
			$connection['database'] = env('DB_DATABASE', ':memory:');
			$connection['foreign_key_constraints'] = true;
		} else {
			$connection['host'] = env('DB_HOST', '127.0.0.1');
			$connection['port'] = env('DB_PORT', $driver === 'pgsql' ? '5432' : '3306');
			$connection['database'] = env('DB_DATABASE', 'congraph_test');
			$connection['username'] = env('DB_USERNAME', 'congraph');
			$connection['password'] = env('DB_PASSWORD', 'congraph');
			$connection['charset'] = 'utf8';
			$connection['prefix_indexes'] = true;
			$connection['schema'] = env('DB_SCHEMA', 'public');
			$connection['sslmode'] = env('DB_SSLMODE', 'prefer');

			if ($driver === 'mysql') {
				$connection['charset'] = 'utf8mb4';
				$connection['collation'] = 'utf8mb4_unicode_ci';
			}
		}

		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', $connection);
	}

	protected function defineDatabaseMigrations()
	{
		$this->loadMigrationsFrom(realpath(__DIR__ . '/../database/migrations'));

		$this->artisan('migrate', ['--database' => 'testbench'])->run();

		$this->beforeApplicationDestroyed(function () {
			$this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
		});
	}

	protected function setUp(): void
	{
		parent::setUp();

		foreach ($this->seeders() as $seeder) {
			$this->artisan('db:seed', [
				'--class' => $seeder,
			]);
		}
	}

	protected function tearDown(): void
	{
		foreach ($this->cleanupSeeders() as $seeder) {
			$this->artisan('db:seed', [
				'--class' => $seeder,
			]);
		}

		parent::tearDown();
	}

	protected function seeders(): array
	{
		return [];
	}

	protected function cleanupSeeders(): array
	{
		return [];
	}

	protected function dispatch(object $command): mixed
	{
		return $this->app->make(CommandDispatcher::class)->dispatch($command);
	}
}
