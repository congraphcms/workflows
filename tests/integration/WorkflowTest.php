<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Debug\Dumper;

require_once(__DIR__ . '/../database/seeders/WorkflowTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/ClearDB.php');

class LocaleTest extends Orchestra\Testbench\TestCase
{

	public function setUp()
	{
		parent::setUp();

		$this->artisan('migrate', [
			'--database' => 'testbench',
			'--realpath' => realpath(__DIR__.'/../database/migrations'),
		]);

		$this->artisan('db:seed', [
			'--class' => 'WorkflowTestDbSeeder'
		]);

		$this->d = new Dumper();


	}

	public function tearDown()
	{
		$this->artisan('db:seed', [
			'--class' => 'ClearDB'
		]);
		parent::tearDown();
	}

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 *
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', [
			'driver'   	=> 'mysql',
			'host'      => '127.0.0.1',
			'port'		=> '33060',
			'database'	=> 'cookbook_testbench',
			'username'  => 'homestead',
			'password'  => 'secret',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		]);

	}

	protected function getPackageProviders($app)
	{
		return ['Cookbook\Workflows\WorkflowsServiceProvider', 'Cookbook\Core\CoreServiceProvider'];
	}

	public function testCreateWorkflow()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'name' => 'Test workflow',
			'description' => 'Just another description'
		];


		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		
		$result = $bus->dispatch( new Cookbook\Workflows\Commands\Workflows\WorkflowCreateCommand($params));
		
		$this->d->dump($result->toArray());
		$this->assertEquals('Test workflow', $result->name);
		$this->assertEquals('Just another description', $result->description);
		
		$this->seeInDatabase('workflows', [
			'id' => 3, 
			'name' => 'Test workflow',
			'description' => 'Just another description'
		]);
	}

	// /**
	//  * @expectedException \Cookbook\Core\Exceptions\ValidationException
	//  */
	// public function testCreateException()
	// {
	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$params = [
	// 		'code' => 'en_enneene',
	// 		'name' => 'English'
	// 	];


	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		
	// 	$result = $bus->dispatch( new Cookbook\Locales\Commands\Locales\LocaleCreateCommand($params));
		
	// }

	// public function testUpdateLocale()
	// {
	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

	// 	$params = [
	// 		'code' => 'en_GB'
	// 	];
		
	// 	$result = $bus->dispatch( new Cookbook\Locales\Commands\Locales\LocaleUpdateCommand($params, 1));
		
	// 	$this->assertTrue($result instanceof Cookbook\Core\Repositories\Model);
	// 	$this->assertTrue(is_int($result->id));
	// 	$this->assertEquals(1, $result->id);
	// 	$this->assertEquals('en_GB', $result->code);
	// 	$this->assertEquals('English', $result->name);
		
	// 	$this->d->dump($result->toArray());
	// }

	// /**
	//  * @expectedException \Cookbook\Core\Exceptions\ValidationException
	//  */
	// public function testUpdateException()
	// {
	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

	// 	$params = [
	// 		'code' => 'en_enene'
	// 	];

	// 	$result = $bus->dispatch( new Cookbook\Locales\Commands\Locales\LocaleUpdateCommand($params, 1));
	// }

	// public function testDeleteLocale()
	// {
	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

	// 	$result = $bus->dispatch( new Cookbook\Locales\Commands\Locales\LocaleDeleteCommand([], 1));

	// 	$this->assertEquals(1, $result);
	// 	$this->d->dump($result);

	// }

	// /**
	//  * @expectedException \Cookbook\Core\Exceptions\NotFoundException
	//  */
	// public function testDeleteException()
	// {
	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

	// 	$result = $bus->dispatch( new Cookbook\Locales\Commands\Locales\LocaleDeleteCommand([], 133));
	// }
	
	// public function testFetchLocale()
	// {

	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

	// 	$result = $bus->dispatch( new Cookbook\Locales\Commands\Locales\LocaleFetchCommand([], 1));

	// 	$this->assertTrue($result instanceof Cookbook\Core\Repositories\Model);
	// 	$this->assertTrue(is_int($result->id));
	// 	$this->assertEquals('en_US', $result->code);
	// 	$this->assertEquals('English', $result->name);
	// 	$this->d->dump($result->toArray());
	// }

	// public function testFetchLocaleByCode()
	// {

	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

	// 	$result = $bus->dispatch( new Cookbook\Locales\Commands\Locales\LocaleFetchCommand([], 'en_US'));

	// 	$this->assertTrue($result instanceof Cookbook\Core\Repositories\Model);
	// 	$this->assertTrue(is_int($result->id));
	// 	$this->assertEquals('en_US', $result->code);
	// 	$this->assertEquals('English', $result->name);
	// 	$this->d->dump($result->toArray());
	// }

	
	// public function testGetLocales()
	// {
	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
	// 	$result = $bus->dispatch( new Cookbook\Locales\Commands\Locales\LocaleGetCommand([]));

	// 	$this->assertTrue($result instanceof Cookbook\Core\Repositories\Collection);
	// 	$this->assertEquals(4, count($result));
	// 	$this->d->dump($result->toArray());

	// }

}