<?php
namespace App\Models;

class ClienteModelEnderecos extends ClienteModelBase
{
    /**
     * Busca os endereços de um cliente
     *
     * @param int $idCliente ID do cliente
     * @return array Lista de endereços
     */
    public function getEnderecos(int $idCliente): array
    {
        try {
            $builder = $this->db->table('enderecos_cliente e');
            $builder->select('e.*, u.estado, u.uf, m.municipio');
            $builder->join('ufs u', 'e.id_uf = u.id_uf', 'left');
            $builder->join('municipios m', 'e.id_municipio = m.id_municipio', 'left');
            $builder->where('e.id_cliente', $idCliente);
            $builder->where('e.deleted_at IS NULL');
            $builder->orderBy('e.tipo', 'ASC');
            
            $query = $builder->get();
            
            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar endereços do cliente: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Salva um endereço adicional para o cliente
     *
     * @param array $dados Dados do endereço
     * @return bool
     */
    public function salvarEndereco(array $dados): bool
    {
        try {
            $this->db->transBegin();
            
            $endereco = [
                'id_cliente' => $dados['id_cliente'],
                'tipo' => $dados['tipo'] ?? 1, // 1 = Entrega, 2 = Cobrança
                'cep' => $dados['cep'] ?? '',
                'logradouro' => $dados['logradouro'] ?? '',
                'numero' => $dados['numero'] ?? '',
                'complemento' => $dados['complemento'] ?? '',
                'bairro' => $dados['bairro'] ?? '',
                'id_uf' => $dados['id_uf'] ?? null,
                'id_municipio' => $dados['id_municipio'] ?? null,
                'responsavel' => $dados['responsavel'] ?? '',
                'documento_responsavel' => $dados['documento_responsavel'] ?? '',
                'id_empresa' => $this->idEmpresa,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->table('enderecos_cliente')->insert($endereco);
            
            $this->db->transCommit();
            
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Erro ao salvar endereço do cliente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualiza um endereço específico
     *
     * @param int $idEndereco ID do endereço
     * @param array $dados Dados do endereço a atualizar
     * @return bool
     */
    public function atualizarEndereco(int $idEndereco, array $dados): bool
    {
        try {
            $this->db->transBegin();
            
            $endereco = [
                'cep' => $dados['cep'] ?? '',
                'logradouro' => $dados['logradouro'] ?? '',
                'numero' => $dados['numero'] ?? '',
                'complemento' => $dados['complemento'] ?? '',
                'bairro' => $dados['bairro'] ?? '',
                'id_uf' => $dados['id_uf'] ?? null,
                'id_municipio' => $dados['id_municipio'] ?? null,
                'responsavel' => $dados['responsavel'] ?? '',
                'documento_responsavel' => $dados['documento_responsavel'] ?? '',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->table('enderecos_cliente')
                     ->where('id_endereco', $idEndereco)
                     ->where('id_empresa', $this->idEmpresa)
                     ->update($endereco);
            
            $this->db->transCommit();
            
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Erro ao atualizar endereço do cliente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove um endereço (soft delete)
     *
     * @param int $idEndereco ID do endereço
     * @return bool
     */
    public function removerEndereco(int $idEndereco): bool
    {
        try {
            $this->db->transBegin();
            
            $this->db->table('enderecos_cliente')
                     ->where('id_endereco', $idEndereco)
                     ->where('id_empresa', $this->idEmpresa)
                     ->update(['deleted_at' => date('Y-m-d H:i:s')]);
            
            $this->db->transCommit();
            
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Erro ao remover endereço do cliente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtém um endereço específico do cliente
     *
     * @param int $idEndereco ID do endereço
     * @param int $idCliente ID do cliente (para verificação de segurança)
     * @return array|null Dados do endereço ou null se não encontrado
     */
    public function getEndereco(int $idEndereco, int $idCliente): ?array
    {
        try {
            $builder = $this->db->table('enderecos_cliente e');
            $builder->select('e.*, u.estado, u.uf, m.municipio');
            $builder->join('ufs u', 'e.id_uf = u.id_uf', 'left');
            $builder->join('municipios m', 'e.id_municipio = m.id_municipio', 'left');
            $builder->where('e.id_endereco', $idEndereco);
            $builder->where('e.id_cliente', $idCliente);
            $builder->where('e.deleted_at IS NULL');
            
            $query = $builder->get();
            $endereco = $query->getRowArray();
            
            return $endereco ?: null;
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar endereço do cliente: " . $e->getMessage());
            return null;
        }
    }
} 