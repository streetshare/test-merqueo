<?php

namespace App\Models;

use CodeIgniter\Model;

class CashModel extends Model
{
    protected $table = 'cash';
    protected $primaryKey = 'id';
    protected $allowedFields = ['counter', 'total'];
    protected $protectFields = true;
    protected $allowedFieldsSelect = ['type', 'code', 'denomination', 'counter'];
    protected $validationRules = [
        'box' => 'required|numeric',
        'type' => 'required',
        'denomination' => 'required|in_list[billete, moneda]',
        'counter' => 'required|numeric'
    ];
    protected $validationMessages = [
        'box' => [
            'required' => 'Caja requerida',
            'numeric' => 'Caja debe ser numérico'
        ],
        'type' => [
            'required' => 'Tipo requerido',
            'in_list' => 'Tipo inválido'
        ],
        'denomination' => [
            'required' => 'Denominación requerido'
        ],
        'counter' => [
            'required' => 'Contador requerido',
            'numeric' => 'Contador debe ser numérico'
        ],
    ];
    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = ['createLog'];
    protected $beforeFind           = [];
    protected $afterFind            = ['mapReturn'];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function get($id = null)
    {
        if ($id == null) {
            return $this->asArray()->where('state', 1)->findAll();
        } else {
            return $this->asArray()->where('state', 1)->find($id);
        }
    }

    public function clearBox($idBox)
    {
        $data = ['counter' => 0, 'total' => 0];
        return $this->where('box', $idBox)->set($data)->update();
    }

    public function loadBox($data)
    {
        $this->where('box', 1);
        $resp = $this->updateBatch($data, 'code');
        if ($resp !== FALSE) {
            $this->where('box', 1);
            $this->set('total', 'denomination * counter', false);
            return $this->update();
        }
        return $resp;
    }

    public function getTotals()
    {
        $this->builder('cash');
        $this->builder->select('type');
        $this->builder->selectSum('total');
        $this->builder->groupBy('type');
        $query = $this->builder->get();
        return $query->getResultArray();
    }

    public function getCounters()
    {
        $this->where('box', 1);
        return $this->asArray()->findAll();
    }

    protected function createLog()
    {
        $logModel = new LogsModel();
        $data = $this->where('box', 1)->findAll();
        $logModel->setLog('cash', 'update', '', $data);
    }

    protected function mapReturn(array $data)
    {
        $resp = $data['data'];
        if (!empty($resp)) {
            if (!empty($resp['id'])) {
                foreach ($resp as $key => $value) {
                    if (!in_array($key, $this->allowedFieldsSelect)) {
                        unset($resp[$key]);
                    }
                }
                $resp = array($resp);
            } else {
                for ($i = 0, $j = COUNT($resp); $i < $j; $i++) {
                    foreach ($resp[$i] as $key => $value) {
                        if (!in_array($key, $this->allowedFieldsSelect)) {
                            unset($resp[$i][$key]);
                        }
                    }
                }
            }
        }
        $data['data'] = $resp;
        return $data;
    }
}
