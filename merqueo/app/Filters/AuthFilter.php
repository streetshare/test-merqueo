<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use Config\Services;
use Firebase\JWT\JWT;

class AuthFilter implements FilterInterface
{
    use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            $key        = Services::getSecretKey();
            $authHeader = $request->getServer('HTTP_AUTHORIZATION');
            if (!empty($authHeader)) {
                $arr        = explode(' ', $authHeader);
                $token      = $arr[1];
                JWT::decode($token, $key, ['HS256']);
            } else {
                return Services::response()
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'No autorizado');
            }
        } catch (\Exception $e) {
            if ($e->getMessage() == 'Expired token') {
                return Services::response()
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'No autorizado');
            } else {
                return Services::response()
                    ->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Error General');
            }
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
