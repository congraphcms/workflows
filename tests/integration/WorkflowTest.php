<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Debug\Dumper;

require_once(__DIR__ . '/../database/seeders/WorkflowTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/ClearDB.php');

class WorkflowTest extends Orchestra\Testbench\TestCase
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
			'port'		=> '3306',
			'database'	=> 'congraph_testbench',
			'username'  => 'root',
			'password'  => '',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		]);

	}

	protected function getPackageProviders($app)
	{
		return ['Congraph\Workflows\WorkflowsServiceProvider', 'Congraph\Core\CoreServiceProvider'];
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
		
		$result = $bus->dispatch( new Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand($params));
		
		$this->d->dump($result->toArray());
		$this->assertEquals('Test workflow', $result->name);
		$this->assertEquals('Just another description', $result->description);
		
		$this->seeInDatabase('workflows', [
			'id' => 3, 
			'name' => 'Test workflow',
			'description' => 'Just another description'
		]);
	}

	/**
	 * @expectedException \Congraph\Core\Exceptions\ValidationException
	 */
	public function testCreateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'name' => ''
		];


		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		
		$result = $bus->dispatch( new Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand($params));
		
	}

	public function testUpdateWorkflow()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$params = [
			'name' => 'Changed Name',
			'description' => ''
		];
		
		$result = $bus->dispatch( new Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand($params, 1));
		
		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals('Changed Name', $result->name);
		$this->assertEquals('', $result->description);
		
		$this->d->dump($result->toArray());
	}

	/**
	 * @expectedException \Congraph\Core\Exceptions\ValidationException
	 */
	public function testUpdateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$params = [
			'name' => ''
		];

		$result = $bus->dispatch( new Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand($params, 1));
	}

	public function testDeleteWorkflow()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand([], 1));

		$this->assertEquals(1, $result);
		$this->d->dump($result);

	}

	/**
	 * @expectedException \Congraph\Core\Exceptions\NotFoundException
	 */
	public function testDeleteException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand([], 133));
	}
	
	public function testFetchWorkflow()
	{

		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand([], 1));

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals('Basic Publishing', $result->name);
		$this->d->dump($result->toArray());
	}

	
	public function testGetWorkflows()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		$result = $bus->dispatch( new Congraph\Workflows\Commands\Workflows\WorkflowGetCommand([]));

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Collection);
		$this->assertEquals(2, count($result));
		$this->d->dump($result->toArray());

	}

}