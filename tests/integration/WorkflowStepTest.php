<?php

use Illuminate\Support\Facades\Cache;
use Symfony\Component\VarDumper\VarDumper as Dumper;

require_once(__DIR__ . '/../database/seeders/WorkflowTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/WorkflowPointTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/WorkflowStepTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/ClearDB.php');

class WorkflowStepTest extends Orchestra\Testbench\TestCase
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

		$this->artisan('db:seed', [
			'--class' => 'WorkflowStepTestDbSeeder'
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

	public function testCreateWorkflowStep()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'workflow_id' => 1,
			'from_id' => 3,
			'to_id' => 1
		];


		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand::class);
		$command->setParams($params);

		$result = $bus->dispatch($command);
		
		$this->d->dump($result->toArray());
		$this->assertEquals(1, $result->workflow_id);
		$this->assertEquals(1, $result->workflow->id);
		$this->assertEquals(3, $result->from_id);
		$this->assertEquals(3, $result->from->id);
		$this->assertEquals(1, $result->to_id);
		$this->assertEquals(1, $result->to->id);
		
		$this->assertDatabaseHas('workflow_steps', [
			'id' => 5, 
			'workflow_id' => 1,
			'from_id' => 3,
			'to_id' => 1
		]);
	}
	

	public function testCreateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$this->expectException(\Congraph\Core\Exceptions\ValidationException::class);

		$params = [ 
			'workflow_id' => 5,
			'from_id' => 3,
			'to_id' => 1
		];


		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepCreateCommand::class);
		$command->setParams($params);

		$result = $bus->dispatch($command);
		
	}

	public function testUpdateWorkflowStep()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'to_id' => 3
		];

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand::class);
		$command->setParams($params);
		$command->setId(1);

		$result = $bus->dispatch($command);
		
		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals(3, $result->to_id);
		$this->assertEquals(1, $result->from_id);
		
		$this->d->dump($result->toArray());
	}


	public function testUpdateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$this->expectException(\Congraph\Core\Exceptions\ValidationException::class);

		$params = [
			'from_id' => 3,
		];

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepUpdateCommand::class);
		$command->setParams($params);
		$command->setId(1);

		$result = $bus->dispatch($command);
	}

	public function testDeleteWorkflowStep()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand::class);
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
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepDeleteCommand::class);
		$command->setId(133);

		$result = $bus->dispatch($command);
	}
	
	public function testFetchWorkflowStep()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepFetchCommand::class);
		$command->setId(1);

		$result = $bus->dispatch($command);

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
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\WorkflowSteps\WorkflowStepGetCommand::class);

		$result = $bus->dispatch($command);

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Collection);
		$this->assertEquals(4, count($result));
		$this->d->dump($result->toArray());

	}

}