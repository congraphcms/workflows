<?php

use Illuminate\Support\Facades\Cache;
use Symfony\Component\VarDumper\VarDumper as Dumper;

require_once(__DIR__ . '/../database/seeders/WorkflowTestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/ClearDB.php');

class WorkflowTest extends Orchestra\Testbench\TestCase
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

	public function testCreateWorkflow()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'name' => 'Test workflow',
			'description' => 'Just another description'
		];


		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand::class);
		$command->setParams($params);

		$result = $bus->dispatch($command);
		
		$this->d->dump($result->toArray());
		$this->assertEquals('Test workflow', $result->name);
		$this->assertEquals('Just another description', $result->description);
		
		$this->assertDatabaseHas('workflows', [
			'id' => 3, 
			'name' => 'Test workflow',
			'description' => 'Just another description'
		]);
	}

	
	public function testCreateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$this->expectException(\Congraph\Core\Exceptions\ValidationException::class);

		$params = [
			'name' => ''
		];


		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\Workflows\WorkflowCreateCommand::class);
		$command->setParams($params);

		$result = $bus->dispatch($command);
		
	}

	public function testUpdateWorkflow()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'name' => 'Changed Name',
			'description' => ''
		];

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand::class);
		$command->setParams($params);
		$command->setId(1);

		$result = $bus->dispatch($command);
		
		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals(1, $result->id);
		$this->assertEquals('Changed Name', $result->name);
		$this->assertEquals('', $result->description);
		
		$this->d->dump($result->toArray());
	}


	public function testUpdateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$this->expectException(\Congraph\Core\Exceptions\ValidationException::class);

		$params = [
			'name' => ''
		];

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\Workflows\WorkflowUpdateCommand::class);
		$command->setParams($params);
		$command->setId(1);

		$result = $bus->dispatch($command);
	}

	public function testDeleteWorkflow()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand::class);
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
		$command = $app->make(\Congraph\Workflows\Commands\Workflows\WorkflowDeleteCommand::class);
		$command->setId(133);

		$result = $bus->dispatch($command);
	}
	
	public function testFetchWorkflow()
	{

		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\Workflows\WorkflowFetchCommand::class);
		$command->setId(1);

		$result = $bus->dispatch($command);

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals('Basic Publishing', $result->name);
		$this->d->dump($result->toArray());
	}

	
	public function testGetWorkflows()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Workflows\Commands\Workflows\WorkflowGetCommand::class);

		$result = $bus->dispatch($command);

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Collection);
		$this->assertEquals(2, count($result));
		$this->d->dump($result->toArray());

	}

}