<?php

namespace App\Models;

use CodeIgniter\Model;

class BoxModel extends Model
{
    protected $table = 'box';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'total', 'cash', 'money'];
    protected $protectFields = true;
    protected $allowedFieldsSelect = ['name', 'total', 'cash', 'money'];
    protected $validationRules = [];
    protected $validationMessages = [];
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
            return $this->asArray()->findAll();
        } else {
            return $this->asArray()->find($id);
        }
    }

    public function setTotals($id, $total = 0, $cash = 0, $money = 0)
    {
        if (is_array($total)) {
            return $this->update($id, $total);
        } else {
            $data = [
                'total' => $total,
                'cash' => $cash,
                'money' => $money
            ];
            return $this->update($id, $data);
        }
    }

    protected function createLog()
    {
        $logModel = new LogsModel();
        $data = $this->where('id', 1)->findAll();
        $logModel->setLog('box', 'update', $data);
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
