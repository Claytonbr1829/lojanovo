<?php
namespace App\Models;

use PDO;
use Exception;
use InvalidArgumentException;
use RuntimeException;

class PedidoModelConsulta extends PedidoModelBase
{
    protected $idEmpresa;

    /**
     * Busca um pedido pelo ID
     *
     * @param int $id ID do pedido
     * @return array|null Dados do pedido ou null se não encontrado
     */
    public function getById(int $id): ?array
    {
        try {
            $query = $this->db->query(
                "SELECT * FROM pedidos WHERE id_pedido = ? AND id_empresa = ?",
                [$id, $this->idEmpresa]
            );

            $pedido = $query->getRowArray();

            if ($pedido) {
                // Busca os itens do pedido
                $pedido['itens'] = $this->getItensByPedido($id);
            }

            return $pedido ?: null;
        } catch (Exception $e) {
            error_log("Erro ao buscar pedido por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca um pedido pelo token
     *
     * @param string $token Token do pedido
     * @return array|null Dados do pedido ou null se não encontrado
     */
    public function getByToken(string $token): ?array
    {
        try {
            $builder = $this->builder();
            $builder->where('token', $token);
            $pedido = $builder->get()->getRowArray();

            if ($pedido) {
                // Busca os itens do pedido
                $pedido['itens'] = $this->getItensByPedido($pedido['id_pedido']);

                // Formata os dados do pedido
                $pedido = $this->formatarPedido($pedido);
            }

            return $pedido ?: null;
        } catch (Exception $e) {
            log_message('error', "Erro ao buscar pedido por token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca os itens de um pedido
     *
     * @param int $idPedido ID do pedido
     * @return array Lista de itens do pedido
     */
    public function getItensByPedido(int $idPedido): array
    {
        try {
            $query = $this->db->query(
                "SELECT i.*, p.nome as nome_produto, p.arquivo as imagem_produto,
                        pc.nome as nome_variacao
                FROM pedidos_itens i
                JOIN produtos p ON i.id_produto = p.id_produto
                LEFT JOIN produtos_combinados pc ON i.id_variacao = pc.id
                WHERE i.id_pedido = ?
                ORDER BY i.id_item ASC",
                [$idPedido]
            );

            return $query->getResultArray();
        } catch (Exception $e) {
            error_log("Erro ao buscar itens do pedido: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca os pedidos de um cliente
     *
     * @param int $idCliente ID do cliente
     * @return array Lista de pedidos do cliente
     */
    public function getByCliente(int $idCliente): array
    {
        try {
            // Debug: verificar parâmetros recebidos
            log_message('debug', 'getByCliente - ID Cliente: ' . $idCliente . ' | ID Empresa: ' . $this->idEmpresa);

            // Verificação dos parâmetros
            if ($idCliente <= 0) {
                throw new InvalidArgumentException('ID do cliente inválido');
            }

            if ($this->idEmpresa <= 0) {
                throw new InvalidArgumentException('ID da empresa inválido');
            }

            // Usando Query Builder para maior segurança e legibilidade
            $builder = $this->db->table('pedidos p');
            $builder->select('p.*, COUNT(i.id_item) as total_itens');
            $builder->join('pedidos_itens i', 'p.id_pedido = i.id_pedido', 'left');
            $builder->where('p.id_cliente', $idCliente);
            $builder->where('p.id_empresa', $this->idEmpresa);
            $builder->groupBy('p.id_pedido');
            $builder->orderBy('p.created_at', 'DESC');

            $query = $builder->get();

            // Debug: verificar a query SQL gerada
            log_message('debug', 'SQL: ' . $builder->getCompiledSelect());

            if (!$query) {
                throw new RuntimeException('Falha ao executar a query: ' . $this->db->error());
            }

            $resultados = $query->getResultArray();

            // Debug: verificar resultados
            log_message('debug', 'Número de pedidos encontrados: ' . count($resultados));

            return $resultados;

        } catch (Exception $e) {
            log_message('error', 'Erro em getByCliente: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca os pedidos recentes da loja
     *
     * @param int $limit Limite de resultados
     * @return array Lista de pedidos recentes
     */
    public function getRecentes(int $limit = 10): array
    {
        try {
            $query = $this->db->query(
                "SELECT p.*, c.nome as nome_cliente, COUNT(i.id_item) as total_itens
                FROM pedidos p
                JOIN clientes c ON p.id_cliente = c.id_cliente
                LEFT JOIN pedidos_itens i ON p.id_pedido = i.id_pedido
                WHERE p.id_empresa = ?
                GROUP BY p.id_pedido
                ORDER BY p.created_at DESC
                LIMIT ?",
                [$this->idEmpresa, $limit]
            );

            return $query->getResultArray();
        } catch (Exception $e) {
            error_log("Erro ao buscar pedidos recentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Conta o total de pedidos por status
     *
     * @return array Array associativo com contagem por status
     */
    public function contarPorStatus(): array
    {
        try {
            $query = $this->db->query(
                "SELECT status, COUNT(*) as total
                FROM pedidos
                WHERE id_empresa = ?
                GROUP BY status",
                [$this->idEmpresa]
            );

            $resultado = $query->getResultArray();

            // Formata o resultado como um array associativo
            $contagem = [];
            foreach ($resultado as $row) {
                $contagem[$row['status']] = $row['total'];
            }

            return $contagem;
        } catch (Exception $e) {
            error_log("Erro ao contar pedidos por status: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca um pedido com detalhes do cliente e pagamento
     *
     * @param string $token Token do pedido
     * @return array Dados detalhados do pedido
     */
    public function findPedidoCompleto(string $token): array
    {
        try {
            $builder = $this->db->table('pedidos p');
            $builder->select('p.*, 
                            CASE 
                                WHEN c.tipo = "2" THEN c.razao_social
                                WHEN c.tipo = "1" THEN c.nome
                                ELSE "Nome não disponível" 
                            END AS nome_cliente,
                            c.email,
                            CASE 
                                WHEN c.tipo = "2" THEN c.cnpj
                                WHEN c.tipo = "1" THEN c.cpf
                                ELSE "Documento não disponível" 
                            END AS documento_cliente');
            $builder->join('clientes c', 'p.id_cliente = c.id_cliente');
            $builder->where('p.token', $token);

            $query = $builder->get();
            $pedido = $query->getRowArray();

            if ($pedido) {
                // Busca os itens do pedido
                $pedido['itens'] = $this->getItensByPedido($pedido['id_pedido']);

                // Busca informações de pagamento
                $builderPagamento = $this->db->table('pedidos_pagamento');
                $builderPagamento->where('id_pedido', $pedido['id_pedido']);
                $query = $builderPagamento->get();
                $pedido['pagamento'] = $query->getRowArray();

                // Formata os dados do pedido
                $pedido = $this->formatarPedido($pedido);
            }

            return $pedido ?: [];
        } catch (Exception $e) {
            log_message('error', "Erro ao buscar o pedido detalhado: " . $e->getMessage());
            return [];
        }
    }

    public function findPedido($token)
    {

    }
}