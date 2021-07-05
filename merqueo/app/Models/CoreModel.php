<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\BoxModel;
use App\Models\CashModel;

class CoreModel extends Model
{
	protected $request;
	protected $boxModel;
	protected $cashModel;

	public function loadModels()
	{
		$this->boxModel = new BoxModel();
		$this->cashModel = new CashModel();
	}

	public function clearBox()
	{
		$this->loadModels();
		$data = $this->boxModel->get(1);
		$this->cashModel->clearBox(1);
		$this->boxModel->setTotals(1);
		return $data;
	}

	public function loadBox($data)
	{
		$data = str_replace(' ', '', $data);
		$data = json_decode($data, true);
		$mapped = [];
		for ($i = 0, $j = COUNT($data); $i < $j; $i++) {
			$row = $data[$i];
			if (!empty($row['code'] && !empty($row['count']))) {
				$mapped[] = array(
					'code' => $row['code'],
					'counter' => $row['count']
				);
			}
		}
		$this->loadModels();
		if (!empty($mapped)) {
			$this->cashModel->loadBox($mapped);
			$data = $this->cashModel->getTotals();
			$totals = [
				'total' => $data[0]['total'] + $data[1]['total'],
				'cash' => $data[0]['total'],
				'money' => $data[1]['total']
			];
			$this->boxModel->setTotals(1, $totals);
		}
		return;
	}

	public function statusBox()
	{
		$this->loadModels();
		$cash = $this->cashModel->getCounters();
		$box = $this->boxModel->get(1);
		return $this->generateStatus($box[0], $cash);
	}

	public function updateCash($arraySum, $arrayMinus)
	{
		$this->loadModels();
		$cash = $this->cashModel->getCounters();
		$mapped = [];
		if (!empty($arraySum)) {
			for ($i = 0, $j = COUNT($arraySum); $i < $j; $i++) {
				$aux = array_filter($cash, function ($key) use ($arraySum, $i) {
					return $key['code'] == $arraySum[$i]['code'];
				}, ARRAY_FILTER_USE_BOTH);
				$aux = array_values($aux);
				if (!empty($aux)) {
					$mapped[] = array(
						'code' => $aux[0]['code'],
						'counter' => $aux[0]['counter'] + $arraySum[$i]['count']
					);
				}
			}
		}
		if (!empty($arrayMinus)) {
			for ($i = 0, $j = COUNT($arrayMinus); $i < $j; $i++) {
				$aux = array_filter($cash, function ($key) use ($arrayMinus, $i) {
					return $key['code'] == $arrayMinus[$i]['code'];
				}, ARRAY_FILTER_USE_BOTH);
				$aux = array_values($aux);
				if (!empty($aux)) {
					$mapped[] = array(
						'code' => $aux[0]['code'],
						'counter' => $aux[0]['counter'] - $arrayMinus[$i]['counter']
					);
				}
			}
		}
		if (!empty($mapped)) {
			$this->cashModel->loadBox($mapped);
			$data = $this->cashModel->getTotals();
			$totals = [
				'total' => $data[0]['total'] + $data[1]['total'],
				'cash' => $data[0]['total'],
				'money' => $data[1]['total']
			];
			$this->boxModel->setTotals(1, $totals);
		}
		return;
	}

	public function validateSell($total)
	{
		$this->loadModels();
		$cashBox = $this->cashModel->getCounters();
		$changeBox = [
			'return' => 0,
			'cash' => []
		];
		$data = $this->mapChange($total, $changeBox, $cashBox);
		if ($data['return'] == $total)
			return $data;
		return FALSE;
	}

	public function mapChange($total, $changeCash, $cashBox)
	{
		$aux = array_filter($cashBox, function ($key)  use ($total) {
			return $key['denomination'] <= $total && $key['counter'] > 0;
		}, ARRAY_FILTER_USE_BOTH);
		$aux = array_values($aux);
		if (!empty($aux)) {
			$count = floor($total / $aux[0]['denomination']);
			if ($count > $aux[0]['counter']) {
				$count = $aux[0]['counter'];
			}
			$changeCash['return'] += $count * $aux[0]['denomination'];
			$changeCash['cash'][] = [
				'type' => $aux[0]['type'],
				'code' => $aux[0]['code'],
				'denomination' => $aux[0]['denomination'],
				'counter' => $count
			];
			$total = $total - ($count * $aux[0]['denomination']);
			if ($count == $aux[0]['counter']) {
				unset($aux[0]);
			}
			return $this->mapChange($total, $changeCash, $aux);
		}
		return $changeCash;
	}

	protected function generateStatus($box, $cash)
	{
		$box['detail'][] = $cash;
		return $box;
	}
}
