<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\JwtService;
use Config\Services;

class JwtFilter implements FilterInterface
{
    /**
     * Verifica se a requisição possui um token JWT válido
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwtService = new JwtService();
        $token = $jwtService->getTokenFromRequest($request);
        
        if (!$token) {
            return Services::response()
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Token não fornecido'
                ]);
        }
        
        $decoded = $jwtService->validateToken($token);
        
        if (!$decoded) {
            return Services::response()
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Token inválido ou expirado'
                ]);
        }
        
        // Adiciona os dados do token à requisição para acesso posterior
        $request->jwt = $decoded;
    }

    /**
     * Este método é executado após o controlador
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Não faz nada após a execução do controlador
        return $response;
    }
} 