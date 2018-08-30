<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Debug\Dumper;

require_once(__DIR__ . '/../database/seeders/WorkflowTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/WorkflowPointTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/WorkflowStepTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/ClearDB.php');

class WorkflowStepTest extends Orchestra\Testbench\TestCase
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

		$this->artisan('db:seed', [
			'--class' => 'WorkflowPointTestDbSeeder'
		]);

		$this->artisan('db:seed', [
			'--class' => 'WorkflowStepTestDbSeeder'
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

	public function testCreateWorkflowStep()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'workflow_id' => 1,
			'from_id' => 3,
			'to_id' => 1
		];


		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		
		$result = $bus->dispatch( new Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand($params));
		
		$this->d->dump($result->toArray());
		$this->assertEquals(1, $result->workflow_id);
		$this->assertEquals(1, $result->workflow->id);
		$this->assertEquals(3, $result->from_id);
		$this->assertEquals(3, $result->from->id);
		$this->assertEquals(1, $result->to_id);
		$this->assertEquals(1, $result->to->id);
		
		$this->seeInDatabase('workflow_steps', [
			'id' => 5, 
			'workflow_id' => 1,
			'from_id' => 3,
			'to_id' => 1
		]);
	}

	/**
	 * @expectedException \Congraph\Core\Exceptions\ValidationException
	 */
	public function testCreateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [ 
			'workflow_id' => 5,
			'from_id' => 3,
			'to_id' => 1
		];


		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		
		$result = $bus->dispatch( new Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand($params));
		
	}

	public function testUpdateWorkflowStep()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$params = [
			'to_id' => 3
		];
		
		$result = $bus->dispatch( new Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand($params, 1));
		
		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals(3, $result->to_id);
		$this->assertEquals(1, $result->from_id);
		
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
			'from_id' => 3,
		];

		$result = $bus->dispatch( new Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand($params, 1));
	}

	public function testDeleteWorkflowStep()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand([], 1));

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

		$result = $bus->dispatch( new Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand([], 133));
	}
	
	public function testFetchWorkflowStep()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand([], 1));

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals(1, $result->workflow_id);
		$this->assertEquals(1, $result->workflow->id);
		$this->assertEquals(1, $result->from_id);
		$this->assertEquals(1, $result->from->id);
		$this->assertEquals(2, $result->to_id);
		$this->assertEquals(2, $result->to->id);
		$this->d->dump($result->toArray());
	}

	
	public function testGetWorkflowSteps()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		$result = $bus->dispatch( new Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand([]));

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Collection);
		$this->assertEquals(4, count($result));
		$this->d->dump($result->toArray());

	}

}