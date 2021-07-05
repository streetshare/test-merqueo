<?php

namespace App\Models;

use CodeIgniter\Model;

class LogsModel extends Model
{
    protected $table = 'logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['table', 'action', 'description', 'data'];
    protected $protectFields = true;
    protected $allowedFieldsSelect = ['table', 'action', 'description', 'data', 'created'];
    protected $validationRules = [
        'table' => 'required|alpha',
        'action' => 'required|in_list[insert, update, in, out]',
        'data' => 'required'
    ];
    protected $validationMessages = [
        'table' => [
            'required' => 'Tabla requerida',
            'alpha' => 'Tabla solo debe tener letras'
        ],
        'action' => [
            'required' => 'Acción requerido',
            'in_list' => 'Acción inválido'
        ],
        'data' => [
            'required' => 'Data requerida'
        ],
    ];
    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = ['mapReturn', 'transfData'];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function getStatus($date)
    {
        $this->whereIn('table', ['box', 'cash']);
        $this->where('created<=', $date);
        $this->orderBy('id', 'desc');
        return $this->asArray()->findAll(2);
    }

    public function getTransactions()
    {
        $this->allowedFieldsSelect = ['description', 'data', 'created'];
        $this->whereIn('action', ['in', 'out']);
        return $this->asArray()->findAll();
    }

    public function setLog($table, $action, $description, $body)
    {
        $data = [
            'table' => $table,
            'action' => $action,
            'description' => $description,
            'data' => json_encode($body)
        ];
        $this->insert($data);
    }

    protected function transfData(array $data)
    {
        $resp = $data['data'];
        if (!empty($resp)) {
            if (!empty($resp['id'])) {
                if (!empty($resp['data'])) {
                    $resp['data'] = json_decode($resp['data']);
                }
            } else {
                for ($i = 0, $j = COUNT($resp); $i < $j; $i++) {
                    if (!empty($resp[$i]['data'])) {
                        $resp[$i]['data'] = json_decode($resp[$i]['data']);
                    }
                }
            }
        }
        $data['data'] = $resp;
        return $data;
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
