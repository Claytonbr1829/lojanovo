<?php

namespace App\Models;

use PDO;
use Exception;

class MarcaParceiraModelDados extends MarcaParceiraModelBase
{
    /**
     * Insere uma nova marca parceira no banco de dados
     *
     * @param array $dados Dados da marca parceira
     * @return bool Resultado da operação
     */
    public function inserirMarcaParceira(array $dados): bool
    {
        try {
            $data = [
                'id_empresa' => $this->idEmpresa,
                'nome' => $dados['nome'],
                'logo' => $dados['logo'],
                'link' => $dados['link'],
                'ativo' => $dados['ativo'],
                'ordem' => $dados['ordem'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $builder = $this->db->table($this->table);
            return $builder->insert($data);
        } catch (\Exception $e) {
            log_message('error', "Erro ao inserir marca parceira: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualiza uma marca parceira existente
     *
     * @param int $id ID da marca parceira
     * @param array $dados Novos dados da marca parceira
     * @return bool Resultado da operação
     */
    public function atualizarMarcaParceira(int $id, array $dados): bool
    {
        try {
            $data = [
                'nome' => $dados['nome'],
                'link' => $dados['link'],
                'ativo' => $dados['ativo'],
                'ordem' => $dados['ordem'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Se tiver logo, adiciona ao array de dados
            if (!empty($dados['logo'])) {
                $data['logo'] = $dados['logo'];
            }
            
            $builder = $this->db->table($this->table);
            $builder->where('id', $id);
            $builder->where('id_empresa', $this->idEmpresa);
            
            return $builder->update($data);
        } catch (\Exception $e) {
            log_message('error', "Erro ao atualizar marca parceira: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Exclui uma marca parceira
     *
     * @param int $id ID da marca parceira
     * @return bool Resultado da operação
     */
    public function excluirMarcaParceira(int $id): bool
    {
        try {
            $builder = $this->db->table($this->table);
            $builder->where('id', $id);
            $builder->where('id_empresa', $this->idEmpresa);
            
            return $builder->delete();
        } catch (\Exception $e) {
            log_message('error', "Erro ao excluir marca parceira: " . $e->getMessage());
            return false;
        }
    }
} 