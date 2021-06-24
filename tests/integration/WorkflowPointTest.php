<?php

use Illuminate\Support\Facades\Cache;
use Symfony\Component\VarDumper\VarDumper as Dumper;

require_once(__DIR__ . '/../database/seeders/WorkflowTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/WorkflowPointTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/ClearDB.php');

class WorkflowPointTest extends Orchestra\Testbench\TestCase
{

	// ----------------------------------------
    // ENVIRONMENT
    // ----------------------------------------

    protected function getPackageProviders($app)
	{
		return ['Congraph\Workflows\WorkflowsServiceProvider', 'Congraph\Core\CoreServiceProvider'];
	}

    /**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 *
	 * @return void
	 */
	protected function defineEnvironment($app)
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

    // ----------------------------------------
    // DATABASE
    // ----------------------------------------

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));

        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
        });
    }


    // ----------------------------------------
    // SETUP
    // ----------------------------------------

    public function setUp(): void {
		parent::setUp();

		$this->d = new Dumper();

        $this->artisan('db:seed', [
			'--class' => 'WorkflowTestDbSeeder'
		]);

		$this->artisan('db:seed', [
			'--class' => 'WorkflowPointTestDbSeeder'
		]);
	}

	public function tearDown(): void {
		$this->artisan('db:seed', [
			'--class' => 'ClearDB'
		]);
		parent::tearDown();
	}

    // ----------------------------------------
    // TESTS **********************************
    // ----------------------------------------

	public function testCreateWorkflowPoint()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'workflow_id' => 1,
			'status' => 'archived',
			'endpoint' => 'archive',
			'action' => 'Archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'public' => true,
			'deleted' => false,
			'sort_order' => 4
		];


		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand::class);
		$command->setParams($params);
		try {
			$result = $bus->dispatch($command);
		} catch (\Congraph\Core\Exceptions\ValidationException $e) {
			$this->d->dump($e->getErrors());
		}
			
		
		$this->d->dump($result->toArray());
		$this->assertEquals('archived', $result->status);
		$this->assertEquals('archive', $result->endpoint);
		$this->assertEquals('Archive', $result->action);
		$this->assertEquals('Archived', $result->name);
		$this->assertEquals('Archived objects', $result->description);
		$this->assertEquals(1, $result->public);
		$this->assertEquals(0, $result->deleted);
		$this->assertEquals(4, $result->sort_order);
		
		$this->assertDatabaseHas('workflow_points', [
			'id' => 4, 
			'status' => 'archived',
			'endpoint' => 'archive',
			'action' => 'Archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'public' => 1,
			'deleted' => 0,
			'sort_order' => 4
		]);
	}

	public function testCreateWorkflowPointWithSteps()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'workflow_id' => 1,
			'status' => 'archived',
			'endpoint' => 'archive',
			'action' => 'Archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'public' => true,
			'deleted' => false,
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
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand::class);
		$command->setParams($params);

		$result = $bus->dispatch($command);
		
		$this->d->dump($result->toArray());
		$this->assertEquals('archived', $result->status);
		$this->assertEquals('archive', $result->endpoint);
		$this->assertEquals('Archive', $result->action);
		$this->assertEquals('Archived', $result->name);
		$this->assertEquals('Archived objects', $result->description);
		$this->assertEquals(1, $result->public);
		$this->assertEquals(0, $result->deleted);
		$this->assertEquals(4, $result->sort_order);
		$this->assertEquals(3, count($result->steps));
		
		$this->assertDatabaseHas('workflow_points', [
			'id' => 4, 
			'status' => 'archived',
			'endpoint' => 'archive',
			'action' => 'Archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'public' => 1,
			'deleted' => 0,
			'sort_order' => 4
		]);

		$this->assertDatabaseHas('workflow_steps', [
			'workflow_id' => 1, 
			'from_id' => 4,
			'to_id' => 1
		]);
		$this->assertDatabaseHas('workflow_steps', [
			'workflow_id' => 1, 
			'from_id' => 4,
			'to_id' => 2
		]);
		$this->assertDatabaseHas('workflow_steps', [
			'workflow_id' => 1, 
			'from_id' => 4,
			'to_id' => 3
		]);

	}

	
	public function testCreateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$this->expectException(\Congraph\Core\Exceptions\ValidationException::class);

		$params = [
			'status' => '',
			'action' => 'archive',
			'name' => 'Archived',
			'description' => 'Archived objects',
			'sort_order' => 4
		];


		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointCreateCommand::class);
		$command->setParams($params);

		$result = $bus->dispatch($command);
		
	}

	public function testUpdateWorkflowPoint()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'status' => 'deleted',
			'action' => 'delete'
		];

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand::class);
		$command->setParams($params);
		$command->setId(1);

		$result = $bus->dispatch($command);
		
		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals('deleted', $result->status);
		$this->assertEquals('delete', $result->action);
		
		$this->d->dump($result->toArray());
	}

	public function testUpdateWorkflowPointWithSteps()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

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

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand::class);
		$command->setParams($params);
		$command->setId(1);

		$result = $bus->dispatch($command);
		
		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals('deleted', $result->status);
		$this->assertEquals('delete', $result->action);
		
		$this->d->dump($result->toArray());
	}
	
	
	public function testUpdateWorkflowPointWithStepToSelf()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$this->expectException(\Congraph\Core\Exceptions\ValidationException::class);

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

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand::class);
		$command->setParams($params);
		$command->setId(1);

		$result = $bus->dispatch($command);
	}

	
	public function testUpdateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$this->expectException(\Congraph\Core\Exceptions\ValidationException::class);

		$params = [
			'status' => 'published',
		];
		
		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointUpdateCommand::class);
		$command->setParams($params);
		$command->setId(1);

		$result = $bus->dispatch($command);
	}

	public function testDeleteWorkflowPoint()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand::class);
		$command->setId(1);

		$result = $bus->dispatch($command);

		$this->assertEquals(1, $result);
		$this->d->dump($result);

	}

	
	public function testDeleteException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$this->expectException(\Congraph\Core\Exceptions\NotFoundException::class);

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointDeleteCommand::class);
		$command->setId(133);

		$result = $bus->dispatch($command);
	}
	
	public function testFetchWorkflowPoint()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointFetchCommand::class);
		$command->setId(1);

		$result = $bus->dispatch($command);

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals('trashed', $result->status);
		$this->d->dump($result->toArray());
	}

	
	public function testGetWorkflowPoints()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowPoints\WorkflowPointGetCommand::class);

		$result = $bus->dispatch($command);

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Collection);
		$this->assertEquals(3, count($result));
		$this->d->dump($result->toArray());

	}

}