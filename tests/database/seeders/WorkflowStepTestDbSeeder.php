<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Database\Seeders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
/**
 * WorkflowStepTestDbSeeder
 * 
 * Seeds Database with needed entries before tests
 * 
 * @uses   		Illuminate\Database\Schema\Blueprint
 * @uses   		Illuminate\Database\Seeder
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowStepTestDbSeeder extends Seeder {

	public function run()
	{
		DB::table('workflow_steps')->delete();
		DB::table('workflow_steps')->insert([
			[
				'id' => 1,
				'workflow_id' => 1,
				'from_id' => 1,
				'to_id' => 2,
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			],
			[
				'id' => 2,
				'workflow_id' => 1,
				'from_id' => 2,
				'to_id' => 1,
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			],
			[
				'id' => 3,
				'workflow_id' => 1,
				'from_id' => 2,
				'to_id' => 3,
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			],
			[
				'id' => 4,
				'workflow_id' => 1,
				'from_id' => 3,
				'to_id' => 2,
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			]
		]);

		$this->syncSequence('workflow_steps', 5);
	}

	private function syncSequence(string $table, int $nextId): void
	{
		$driver = DB::getDriverName();

		if ($driver === 'pgsql') {
			DB::statement(
				"SELECT setval(pg_get_serial_sequence('{$table}', 'id'), ?, false)",
				[$nextId]
			);
			return;
		}

		if ($driver === 'sqlite') {
			try {
				DB::table('sqlite_sequence')->updateOrInsert(
					['name' => $table],
					['seq' => $nextId - 1]
				);
			} catch (\Throwable $e) {
			}
			return;
		}

		if ($driver === 'mysql') {
			DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = {$nextId}");
		}
	}
}
