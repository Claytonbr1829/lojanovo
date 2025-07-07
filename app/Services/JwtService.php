<?php
namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\Config\Services;

class JwtService
{
    private $secret;
    private $expires;
    private $issuer;

    public function __construct()
    {
        // No CodeIgniter 4, usamos o arquivo .env para configurações
        $this->secret = getenv('jwt.secret') ?: 'swapshop_secret_key';
        $this->expires = getenv('jwt.expires') ? (int)getenv('jwt.expires') : 3600;
        $this->issuer = getenv('jwt.issuer') ?: 'SwapShop ERP';
    }

    /**
     * Gera um token JWT para o usuário
     *
     * @param array $userData Dados do usuário a serem incluídos no token
     * @return string Token JWT
     */
    public function generateToken(array $userData): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->expires;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'iss' => $this->issuer,
            'data' => $userData
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * Valida e decodifica um token JWT
     *
     * @param string $token Token JWT a ser validado
     * @return object|false Payload decodificado ou false em caso de erro
     */
    public function validateToken(string $token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtém o token da requisição
     *
     * @param object $request Objeto de requisição do CodeIgniter
     * @return string|null Token JWT ou null se não encontrado
     */
    public function getTokenFromRequest($request): ?string
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }
} 