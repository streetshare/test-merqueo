<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Cash extends Migration
{
	public function up()
	{
		$this->forge->addField('id');
		$this->forge->addField([
			'box'       => [
				'type'       => 'INT',
				'constraint' => '5',
			],
			'type'       => [
				'type'       => 'VARCHAR',
				'constraint' => '10',
			],
			'code' => [
				'type'       => 'VARCHAR',
				'constraint' => '10',
			],
			'denomination'       => [
				'type'       => 'INT',
				'constraint' => '10',
			],
			'counter'       => [
				'type'       => 'INT',
				'constraint' => '5',
			],
			'total'       => [
				'type'       => 'INT',
				'constraint' => '10',
			],

		]);
		$this->forge->createTable('cash');
	}

	public function down()
	{
		$this->forge->dropTable('cash');
	}
}
