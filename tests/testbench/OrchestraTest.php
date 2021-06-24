<?php

namespace Tests\Testbench;


use Symfony\Component\VarDumper\VarDumper as Dumper;

require_once(__DIR__ . '/../database/seeders/WorkflowTestDbSeeder.php');

class OrchestraTest extends \Orchestra\Testbench\TestCase
{
    // ----------------------------------------
    // ENVIRONMENT
    // ----------------------------------------

    protected function getPackageProviders($app)
	{
		return ['Congraph\Workflows\WorkflowsServiceProvider', 'Congraph\Core\CoreServiceProvider'];
	}

    /**
     * Override default application providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function overrideApplicationBindings($app)
    {
        return [
            'Illuminate\Bus\BusServiceProvider' => 'Congraph\Core\Bus\BusServiceProvider',
        ];
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
        $this->artisan('db:seed', [
			'--class' => 'WorkflowTestDbSeeder'
		]);
		$this->d = new Dumper();
	}

    // ----------------------------------------
    // TESTS **********************************
    // ----------------------------------------

    public function testTheTest() {
        fwrite(STDOUT, __METHOD__ . "\n");

        $this->assertEquals(1, 1);
    }
}