<?php
namespace App\Models;

class ClienteModelDados extends ClienteModelBase
{
    protected $idEmpresa;

    /**
     * Busca um cliente pelo e-mail
     *
     * @param string $email E-mail do cliente
     * @return array|null Dados do cliente ou null se não encontrado
     */
    public function getByEmail(string $email): ?array
    {
        try {
            return $this->where('email', $email)
                ->where('id_empresa', $this->idEmpresa)
                ->first();
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar cliente por e-mail: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca um cliente pelo ID
     *
     * @param int $id ID do cliente
     * @return array|null Dados do cliente ou null se não encontrado
     */
    public function getById(int $id, int $idEmpresa)
    {
        try {
            $byId = $this->where('id_cliente', $id)
                       ->where('id_empresa', $idEmpresa)
                       ->first();

            return $byId;

                       
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar cliente por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtém os dados de um cliente pelo ID com status ativo
     *
     * @param int $idCliente ID do cliente
     * @return array|null Dados do cliente ou null se não encontrado
     */
    public function getCliente(int $idCliente): ?array
    {
        try {
            return $this->where('id_cliente', $idCliente)
                ->where('status', 1)
                ->where('id_empresa', $this->idEmpresa)
                ->first();
        } catch (\Exception $e) {
            log_message('error', "Erro ao obter cliente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Atualiza os dados do cliente
     *
     * @param int $idCliente ID do cliente
     * @param array $dados Dados a serem atualizados
     * @return bool
     */
    public function atualizarDados(int $idCliente, array $dados): bool
    {
        try {
            $this->db->transBegin();

            $dadosUpdate = [
                'nome' => $dados['nome'] ?? '',
                'email' => $dados['email'] ?? '',
                'celular_1' => $dados['telefone'] ?? '',
                'cep' => $dados['cep'] ?? '',
                'logradouro' => $dados['logradouro'] ?? '',
                'numero' => $dados['numero'] ?? '',
                'complemento' => $dados['complemento'] ?? '',
                'bairro' => $dados['bairro'] ?? ''
            ];

            $this->update($idCliente, $dadosUpdate);

            $this->db->transCommit();

            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Erro ao atualizar dados do cliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Salva um novo cliente
     *
     * @param array $dados Dados do cliente
     * @return int ID do cliente inserido ou 0 em caso de erro
     */
    public function salvar($dados)
    {
        $db = \Config\Database::connect(); // Conexão direta

        try {
            // Verificação de campos obrigatórios
            if (empty($dados['email']) || empty($dados['senha']) || empty($dados['id_empresa'])) {
                throw new \RuntimeException('Campos obrigatórios faltando');
            }

            $dadosParaInserir = [
                'email' => $dados['email'],
                'senha' => password_hash($dados['senha'], PASSWORD_DEFAULT),
                'id_empresa' => (int) $dados['id_empresa'], // Conversão explícita
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ];

            // DEBUG: Mostra dados antes de inserir
            log_message('debug', 'Dados para inserção: ' . print_r($dadosParaInserir, true));

            // Usa Query Builder diretamente para mais controle
            $builder = $db->table('clientes');
            $builder->insert($dadosParaInserir);


            if ($db->affectedRows() === 0) {
                $error = $db->error();
                log_message('error', 'Falha na inserção: ' . json_encode($error));
                return 0;
            }

            return $db->insertID(); // Retorna o ID do novo registro

        } catch (\Exception $e) {
            log_message('error', "Erro ao salvar cliente: " . $e->getMessage());
            return 0;
        }
    }

    public function emailExiste($email)
    {
        return $this->where('email', $email)->countAllResults() > 0;
    }
}