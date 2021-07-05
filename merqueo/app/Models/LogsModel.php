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
        'action' => 'required|in_list[insert, update]',
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
    protected $afterFind            = ['mapReturn'];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function get()
    {
        return $this->asArray()->findAll();
    }

    public function getBox()
    {
        return $this->asArray()->where('table', 'box')->findAll();
    }

    public function setLog($table, $action, $body)
    {
        $data = [
            'table' => $table,
            'action' => $action,
            'data' => json_encode($body)
        ];
        $this->insert($data);
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
