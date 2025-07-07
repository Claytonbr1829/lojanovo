<?php
namespace App\Models;

/**
 * Modelo principal para clientes, que integra todas as funcionalidades
 * divididas em arquivos separados para melhor organização.
 */
class ClienteModel extends ClienteModelBase
{
    // Inclui classes de funcionalidades específicas
    private $modelAutenticacao;
    private $modelDados;
    private $modelEnderecos;

    public function __construct()
    {
        parent::__construct();

        // Instancia os modelos específicos
        $this->modelAutenticacao = new ClienteModelAutenticacao();
        $this->modelDados = new ClienteModelDados();
        $this->modelEnderecos = new ClienteModelEnderecos();
    }

    // Métodos ClienteModelAutenticacao

    /**
     * Verifica as credenciais do cliente e retorna os dados se forem válidas
     */
    public function autenticar($senha, $cliente)
    {
        return $this->modelAutenticacao->autenticar($senha, $cliente);
    }

    /**
     * Salva token de autenticação para "lembrar-me"
     */
    public function salvarToken(int $idCliente, string $token, int $expira): bool
    {
        return $this->modelAutenticacao->salvarToken($idCliente, $token, $expira);
    }

    /**
     * Salva token para recuperação de senha
     */
    public function salvarTokenRecuperacao(int $idCliente, string $token): bool
    {
        return $this->modelAutenticacao->salvarTokenRecuperacao($idCliente, $token);
    }

    /**
     * Atualiza a senha do cliente
     */
    public function atualizarSenha(int $idCliente, string $novaSenha): bool
    {
        return $this->modelAutenticacao->atualizarSenha($idCliente, $novaSenha);
    }

    /**
     * Verifica se um e-mail já está cadastrado
     */
    public function emailExiste(string $email): bool
    {
        return $this->modelAutenticacao->emailExiste($email);
    }

    // Métodos ClienteModelDados

    /**
     * Busca um cliente pelo e-mail
     */
    public function getByEmail(string $email): ?array
    {
        return $this->modelDados->getByEmail($email);
    }

    /**
     * Busca um cliente pelo ID
     */
    public function getById(int $id, int $idEmpresa)
    {
        return $this->modelDados->getById($id, $idEmpresa);
    }

    /**
     * Obtém os dados de um cliente pelo ID com status ativo
     */
    public function getCliente(int $idCliente): ?array
    {
        return $this->modelDados->getCliente($idCliente);
    }

    /**
     * Atualiza os dados do cliente
     */
    public function atualizarDados(int $idCliente, array $dados): bool
    {
        return $this->modelDados->atualizarDados($idCliente, $dados);
    }

    /**
     * Salva um novo cliente
     */
    public function salvar($dados)
    {
        return $this->modelDados->salvar($dados);
    }

    // Métodos ClienteModelEnderecos

    /**
     * Busca os endereços de um cliente
     */
    public function getEnderecos(int $idCliente): array
    {
        return $this->modelEnderecos->getEnderecos($idCliente);
    }

    /**
     * Salva um endereço adicional para o cliente
     */
    public function salvarEndereco(array $dados): bool
    {
        return $this->modelEnderecos->salvarEndereco($dados);
    }

    /**
     * Atualiza um endereço específico
     */
    public function atualizarEndereco(int $idEndereco, array $dados): bool
    {
        return $this->modelEnderecos->atualizarEndereco($idEndereco, $dados);
    }

    /**
     * Remove um endereço (soft delete)
     */
    public function removerEndereco(int $idEndereco): bool
    {
        return $this->modelEnderecos->removerEndereco($idEndereco);
    }

    /**
     * Obtém um endereço específico do cliente
     */
    public function getEndereco(int $idEndereco, int $idCliente): ?array
    {
        return $this->modelEnderecos->getEndereco($idEndereco, $idCliente);
    }

}