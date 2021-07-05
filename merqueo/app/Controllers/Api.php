<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CoreModel;
use Firebase\JWT\JWT;

class Api extends ResourceController
{
    protected $request;
    protected $coreModel;

    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->coreModel = new CoreModel();
    }

    /** Autenticación */
    public function auth()
    {
        $token = $this->generateJWT(1);
        $body = ['token' => $token];
        return $this->genericResponse(200, 'token creado', $body);
    }

    /** Vaciar Caja */
    public function clearBox()
    {
        $resp = $this->coreModel->clearBox();
        return $this->genericResponse(200, 'Caja vacía', $resp);
    }

    /** Venta en caja */
    public function sellBox()
    {
        $data = $this->request->getBody();
        if (!empty($data)) {
            $data = str_replace(' ', '', $data);
            $data = json_decode($data, true);
            $total = $data['total'] - $data['payment'];
            $resp = $this->coreModel->validateSell($total);
            if ($resp !== FALSE) {
                $this->coreModel->updateCash($data['counter'], $resp['cash']);
                return $this->genericResponse(200, 'Venta realizada', $resp);
            } else {
                return $this->genericResponse(400, 'No hay cambio suficiente', []);
            }
        }
        return $this->genericResponse(400, 'Falta información', []);
    }

    /** Carga Inicial de la caja */
    public function loadBox()
    {
        $arrayCash = $this->request->getBody();
        if (!empty($arrayCash)) {
            $resp = $this->coreModel->loadBox($arrayCash);
            if ($resp === FALSE)
                return $this->genericResponse(500, 'Error de carga', []);
            return $this->genericResponse(200, 'Caja cargada', []);
        } else {
            return $this->genericResponse(400, 'Falta Información', []);
        }
    }

    /** Registro logs de la caja */
    public function logsBox()
    {
        $data = $this->coreModel->logsBox();
        return $this->genericResponse(200, 'Logs de Caja', $data);
    }

    /** Estado de la Caja
     *  - Actual
     *  - Por fecha
     */
    public function statusBox()
    {
        $date = $this->request->getPost('date');
        $data = $this->coreModel->statusBox($date);
        return $this->genericResponse(200, 'Estado Caja', $data);
    }

    /** Respuesta genérica del api */
    private function genericResponse($code, $message, $body)
    {
        return $this->respond(array('code' => $code, 'message' => $message, 'body' => $body));
    }

    private function generateJWT($id)
    {
        $key = service("getSecretKey");
        $time = time();
        $payload = [
            'aud' => base_url(),
            'id' => $id,
            'iat' => $time,
            'exp' => $time + 600     // tiempo en segundos
        ];
        $jwt = JWT::encode($payload, $key);
        return $jwt;
    }
}
