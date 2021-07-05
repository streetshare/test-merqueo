<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Box extends Migration
{
	public function up()
	{
		$this->forge->addField('id');
		$this->forge->addField([
			'name'       => [
				'type'       => 'VARCHAR',
				'constraint' => '20',
			],
			'total'       => [
				'type'       => 'INT',
				'constraint' => '10',
			],
			'cash'       => [
				'type'       => 'INT',
				'constraint' => '10',
			],
			'money'       => [
				'type'       => 'INT',
				'constraint' => '10',
			],
		]);
		$this->forge->createTable('box');
	}

	public function down()
	{
		$this->forge->dropTable('box');
	}
}
