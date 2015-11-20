<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Debug\Dumper;

require_once(__DIR__ . '/../database/seeders/WorkflowTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/WorkflowPointTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/ClearDB.php');

class WorkflowPointTest extends Orchestra\Testbench\TestCase
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

	public function testCreateWorkflowPoint()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'workflow_id' => 1,
			'status' => 'archived',
			'action' => 'archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'sort_order' => 4
		];


		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		
		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand($params));
		
		$this->d->dump($result->toArray());
		$this->assertEquals('archived', $result->status);
		$this->assertEquals('archive', $result->action);
		$this->assertEquals('Archived', $result->name);
		$this->assertEquals('Archived objects', $result->description);
		$this->assertEquals(4, $result->sort_order);
		
		$this->seeInDatabase('workflow_points', [
			'id' => 4, 
			'status' => 'archived',
			'action' => 'archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'sort_order' => 4
		]);
	}

	public function testCreateWorkflowPointWithSteps()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'workflow_id' => 1,
			'status' => 'archived',
			'action' => 'archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'sort_order' => 4,
			'steps' => [
				[
					'id' => 1,
					'type' => 'workflow-point'
				],
				[
					'id' => 2,
					'type' => 'workflow-point'
				],
				[
					'id' => 3,
					'type' => 'workflow-point'
				]
			]
		];


		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		
		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand($params));
		
		$this->d->dump($result->toArray());
		$this->assertEquals('archived', $result->status);
		$this->assertEquals('archive', $result->action);
		$this->assertEquals('Archived', $result->name);
		$this->assertEquals('Archived objects', $result->description);
		$this->assertEquals(4, $result->sort_order);
		$this->assertEquals(3, count($result->steps));
		
		$this->seeInDatabase('workflow_points', [
			'id' => 4, 
			'status' => 'archived',
			'action' => 'archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'sort_order' => 4
		]);

		$this->seeInDatabase('workflow_steps', [
			'workflow_id' => 1, 
			'from_id' => 4,
			'to_id' => 1
		]);
		$this->seeInDatabase('workflow_steps', [
			'workflow_id' => 1, 
			'from_id' => 4,
			'to_id' => 2
		]);
		$this->seeInDatabase('workflow_steps', [
			'workflow_id' => 1, 
			'from_id' => 4,
			'to_id' => 3
		]);

	}

	/**
	 * @expectedException \Cookbook\Core\Exceptions\ValidationException
	 */
	public function testCreateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'status' => '',
			'action' => 'archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'sort_order' => 4
		];


		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		
		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand($params));
		
	}

	public function testUpdateWorkflowPoint()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$params = [
			'status' => 'deleted',
			'action' => 'delete'
		];
		
		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand($params, 1));
		
		$this->assertTrue($result instanceof Cookbook\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals('deleted', $result->status);
		$this->assertEquals('delete', $result->action);
		
		$this->d->dump($result->toArray());
	}

	public function testUpdateWorkflowPointWithSteps()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$params = [
			'status' => 'deleted',
			'action' => 'delete',
			'steps' => [
				[
					'id' => 2,
					'type' => 'workflow-point'
				],
				[
					'id' => 3,
					'type' => 'workflow-point'
				]
			]
		];
		
		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand($params, 1));
		
		$this->assertTrue($result instanceof Cookbook\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals('deleted', $result->status);
		$this->assertEquals('delete', $result->action);
		
		$this->d->dump($result->toArray());
	}
	
	/**
	 * @expectedException \Cookbook\Core\Exceptions\ValidationException
	 */
	public function testUpdateWorkflowPointWithStepToSelf()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$params = [
			'status' => 'deleted',
			'action' => 'delete',
			'steps' => [
				[
					'id' => 1,
					'type' => 'workflow-point'
				],
				[
					'id' => 2,
					'type' => 'workflow-point'
				],
				[
					'id' => 3,
					'type' => 'workflow-point'
				]
			]
		];
		
		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand($params, 1));
	}

	/**
	 * @expectedException \Cookbook\Core\Exceptions\ValidationException
	 */
	public function testUpdateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$params = [
			'status' => 'published',
		];

		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand($params, 1));
	}

	public function testDeleteWorkflowPoint()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand([], 1));

		$this->assertEquals(1, $result);
		$this->d->dump($result);

	}

	/**
	 * @expectedException \Cookbook\Core\Exceptions\NotFoundException
	 */
	public function testDeleteException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand([], 133));
	}
	
	public function testFetchWorkflowPoint()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand([], 1));

		$this->assertTrue($result instanceof Cookbook\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals('trashed', $result->status);
		$this->d->dump($result->toArray());
	}

	
	public function testGetWorkflowPoints()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		$result = $bus->dispatch( new Cookbook\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand([]));

		$this->assertTrue($result instanceof Cookbook\Core\Repositories\Collection);
		$this->assertEquals(3, count($result));
		$this->d->dump($result->toArray());

	}

}