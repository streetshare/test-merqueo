<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Log extends Migration
{
	public function up()
	{
		$this->forge->addField('id');
		$this->forge->addField([
			'table'       => [
				'type'       => 'VARCHAR',
				'constraint' => '20',
			],
			'action'       => [
				'type'       => 'VARCHAR',
				'constraint' => '20',
			],
			'description'       => [
				'type'       => 'VARCHAR',
				'constraint' => '100',
			],
			'data' =>[
				'type' => 'TEXT',
			],
			'created' => [
				'type'       => 'TIMESTAMP',
				'default' => date('Y-m-d H:i:s')
			],

		]);
		$this->forge->createTable('logs');
	}

	public function down()
	{
		$this->forge->dropTable('logs');
	}
}
