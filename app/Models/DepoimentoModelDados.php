<?php

namespace App\Models;

use PDO;
use Exception;

class DepoimentoModelDados extends DepoimentoModelBase
{
    /**
     * Insere um novo depoimento no banco de dados
     *
     * @param array $dados Dados do depoimento
     * @return bool Resultado da operação
     */
    public function inserirDepoimento(array $dados): bool
    {
        try {
            $query = "
                INSERT INTO loja_depoimentos (
                    id_empresa,
                    nome_cliente,
                    cargo_empresa,
                    depoimento,
                    avaliacao,
                    foto,
                    ordem,
                    ativo,
                    created_at
                ) VALUES (
                    :id_empresa,
                    :nome_cliente,
                    :cargo_empresa,
                    :depoimento,
                    :avaliacao,
                    :foto,
                    :ordem,
                    :ativo,
                    NOW()
                )
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':nome_cliente', $dados['nome_cliente'], PDO::PARAM_STR);
            $stmt->bindParam(':cargo_empresa', $dados['cargo_empresa'], PDO::PARAM_STR);
            $stmt->bindParam(':depoimento', $dados['depoimento'], PDO::PARAM_STR);
            $stmt->bindParam(':avaliacao', $dados['avaliacao'], PDO::PARAM_INT);
            $stmt->bindParam(':foto', $dados['foto'] ?? '', PDO::PARAM_STR);
            $stmt->bindParam(':ordem', $dados['ordem'], PDO::PARAM_INT);
            $stmt->bindParam(':ativo', $dados['ativo'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erro ao inserir depoimento: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualiza um depoimento existente
     *
     * @param int $id ID do depoimento
     * @param array $dados Dados atualizados
     * @return bool Resultado da operação
     */
    public function atualizarDepoimento(int $id, array $dados): bool
    {
        try {
            // Verificar se o depoimento existe e pertence à empresa
            $modelConsulta = new DepoimentoModelConsulta();
            $depoimento = $modelConsulta->getDepoimento($id);
            
            if (!$depoimento) {
                return false;
            }
            
            // Construir a query de atualização
            $query = "
                UPDATE loja_depoimentos
                SET 
                    nome_cliente = :nome_cliente,
                    cargo_empresa = :cargo_empresa,
                    depoimento = :depoimento,
                    avaliacao = :avaliacao,
                    ordem = :ordem,
                    ativo = :ativo,
                    updated_at = NOW()
            ";
            
            // Adicionar foto apenas se foi enviada
            if (isset($dados['foto']) && !empty($dados['foto'])) {
                $query .= ", foto = :foto";
            }
            
            $query .= " WHERE id = :id AND id_empresa = :id_empresa";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':nome_cliente', $dados['nome_cliente'], PDO::PARAM_STR);
            $stmt->bindParam(':cargo_empresa', $dados['cargo_empresa'], PDO::PARAM_STR);
            $stmt->bindParam(':depoimento', $dados['depoimento'], PDO::PARAM_STR);
            $stmt->bindParam(':avaliacao', $dados['avaliacao'], PDO::PARAM_INT);
            $stmt->bindParam(':ordem', $dados['ordem'], PDO::PARAM_INT);
            $stmt->bindParam(':ativo', $dados['ativo'], PDO::PARAM_INT);
            
            // Bind foto apenas se foi enviada
            if (isset($dados['foto']) && !empty($dados['foto'])) {
                $stmt->bindParam(':foto', $dados['foto'], PDO::PARAM_STR);
            }
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erro ao atualizar depoimento: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Exclui um depoimento
     *
     * @param int $id ID do depoimento
     * @return bool Resultado da operação
     */
    public function excluirDepoimento(int $id): bool
    {
        try {
            // Verificar se o depoimento existe e pertence à empresa
            $modelConsulta = new DepoimentoModelConsulta();
            $depoimento = $modelConsulta->getDepoimento($id);
            
            if (!$depoimento) {
                return false;
            }
            
            $query = "
                DELETE FROM loja_depoimentos
                WHERE id = :id AND id_empresa = :id_empresa
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erro ao excluir depoimento: " . $e->getMessage());
            return false;
        }
    }
} 