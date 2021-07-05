<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Init extends Seeder
{
	public function run()
	{
		$data = [
			'name' => 'Caja 1',
			'total' => 0
		];
		$this->db->table('box')->insert($data);
		$data = [
			[
				'box' => 1,
				'type' => 'billete',
				'code' => 'b100',
				'denomination' => 100000,
				'counter' => 0, 'total' => 0
			],
			[
				'box' => 1,
				'type' => 'billete',
				'code' => 'b50',
				'denomination' => 50000,
				'counter' => 0, 'total' => 0
			],
			[
				'box' => 1,
				'type' => 'billete',
				'code' => 'b20',
				'denomination' => 20000,
				'counter' => 0, 'total' => 0
			],
			[
				'box' => 1,
				'type' => 'billete',
				'code' => 'b10',
				'denomination' => 10000,
				'counter' => 0, 'total' => 0
			],
			[
				'box' => 1,
				'type' => 'billete',
				'code' => 'b5',
				'denomination' => 5000,
				'counter' => 0, 'total' => 0
			],
			[
				'box' => 1,
				'type' => 'billete',
				'code' => 'b1',
				'denomination' => 1000,
				'counter' => 0, 'total' => 0
			],
			[
				'box' => 1,
				'type' => 'moneda',
				'code' => 'm1000',
				'denomination' => 1000,
				'counter' => 0, 'total' => 0
			],
			[
				'box' => 1,
				'type' => 'moneda',
				'code' => 'm500',
				'denomination' => 500,
				'counter' => 0, 'total' => 0
			],
			[
				'box' => 1,
				'type' => 'moneda',
				'code' => 'm200',
				'denomination' => 200,
				'counter' => 0, 'total' => 0
			],
			[
				'box' => 1,
				'type' => 'moneda',
				'code' => 'm100',
				'denomination' => 100,
				'counter' => 0, 'total' => 0
			],
			[
				'box' => 1,
				'type' => 'moneda',
				'code' => 'm50',
				'denomination' => 50,
				'counter' => 0, 'total' => 0
			],
		];
		$this->db->table('cash')->insertBatch($data);
	}
}
