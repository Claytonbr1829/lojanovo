<?php
namespace App\Models;

class ClienteModelAutenticacao extends ClienteModelBase
{
    /**
     * Verifica as credenciais do cliente e retorna os dados se forem válidas
     *
     * @param string $email E-mail do cliente
     * @param string $senha Senha do cliente
     * @return array|null Dados do cliente ou null se não encontrado/autenticado
     */
    public function autenticar($senha,$cliente )
    {

        try {
            if (!$cliente) {
                return null;
            }

            // Verifica senha com hash
            if (!empty($cliente->senha_novo) && password_verify($senha, $cliente->senha_novo)) {
                return $cliente;
            }

            // Verificação legada (texto puro)
            if (!empty($cliente->senha)) {  
                $this->atualizarSenha($cliente->id_cliente, $senha);
                return $cliente;
            }
            
            return null;

        } catch (\Exception $e) {
            log_message('error', "Erro ao autenticar cliente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Salva token de autenticação para "lembrar-me"
     *
     * @param int $idCliente ID do cliente
     * @param string $token Token de autenticação
     * @param int $expira Tempo de expiração em segundos
     * @return bool
     */
    public function salvarToken(int $idCliente, string $token, int $expira): bool
    {
        try {
            $db = \Config\Database::connect();

            // Primeiro remove tokens antigos deste cliente
            $db->table('cliente_tokens')
                ->where('id_cliente', $idCliente)
                ->where('tipo', 'auth')
                ->delete();

            // Calcula data de expiração
            $expiraEm = date('Y-m-d H:i:s', time() + $expira);

            // Insere o novo token
            $data = [
                'id_cliente' => $idCliente,
                'token' => $token,
                'tipo' => 'auth',
                'expira_em' => $expiraEm,
                'created_at' => date('Y-m-d H:i:s')
            ];

            return $db->table('cliente_tokens')->insert($data);
        } catch (\Exception $e) {
            log_message('error', "Erro ao salvar token de autenticação: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Salva token para recuperação de senha
     *
     * @param int $idCliente ID do cliente
     * @param string $token Token de recuperação
     * @return bool
     */
    public function salvarTokenRecuperacao(int $idCliente, string $token): bool
    {
        try {
            $db = \Config\Database::connect();

            // Remove tokens antigos deste cliente
            $db->table('cliente_tokens')
                ->where('id_cliente', $idCliente)
                ->where('tipo', 'recuperacao')
                ->delete();

            // Calcula expiração para 24 horas
            $expiraEm = date('Y-m-d H:i:s', time() + (24 * 60 * 60));

            // Insere o novo token
            $data = [
                'id_cliente' => $idCliente,
                'token' => $token,
                'tipo' => 'recuperacao',
                'expira_em' => $expiraEm,
                'created_at' => date('Y-m-d H:i:s')
            ];

            return $db->table('cliente_tokens')->insert($data);
        } catch (\Exception $e) {
            log_message('error', "Erro ao salvar token de recuperação: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza a senha do cliente
     *
     * @param int $idCliente ID do cliente
     * @param string $novaSenha Nova senha em texto plano
     * @return bool
     */
    public function atualizarSenha(int $idCliente, string $novaSenha): bool
    {
        try {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

            return $this->update($idCliente, [
                'senha_novo' => $senhaHash
            ]);
        } catch (\Exception $e) {
            log_message('error', "Erro ao atualizar senha: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica se um e-mail já está cadastrado
     *
     * @param string $email E-mail para verificar
     * @return bool
     */
    public function emailExiste(string $email): bool
    {
        try {
            return $this->where('email', $email)
                ->where('id_empresa', $this->idEmpresa)
                ->countAllResults() > 0;
        } catch (\Exception $e) {
            log_message('error', "Erro ao verificar e-mail: " . $e->getMessage());
            return false;
        }
    }
}