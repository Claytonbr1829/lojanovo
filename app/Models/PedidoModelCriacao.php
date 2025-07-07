<?php
namespace App\Models;

use PDO;
use Exception;

class PedidoModelCriacao extends PedidoModelBase
{
    /**
     * Cria um novo pedido
     *
     * @param array $dados Dados do pedido
     * @return int ID do pedido criado ou 0 em caso de erro
     */
    public function criar(array $pedido): int
    {
        try {
            // Inicia transação
            $this->db->transStart();

            // Insere o pedido usando Query Builder
            $this->db->table('pedidos')->insert($pedido);

            // Pega o ID do pedido inserido
            $idPedido = $this->db->insertID();

            // Finaliza transação (commit)
            $this->db->transComplete();

            return $idPedido;
        } catch (Exception $e) {
            // Rollback automático se ocorrer erro
            log_message('error', 'Erro ao criar pedido: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Adiciona um item ao pedido
     *
     * @param int $idPedido ID do pedido
     * @param array $item Dados do item
     * @return bool Sucesso ou falha
     */
    public function adicionarItem(int $idPedido, array $item): bool
    {
        try {
            $dados = [
                'id_pedido' => $idPedido,
                'id_produto' => $item['id_produto'],
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco'],
                'subtotal' => $item['preco'] * $item['quantidade']
            ];

            // Adiciona a variação se existir
            if (isset($item['id_variacao']) && $item['id_variacao'] > 0) {
                $dados['id_variacao'] = $item['id_variacao'];
            }

            $builder = $this->db->table('pedidos_itens');
            $success = $builder->insert($dados);

            if ($success) {
                // Atualiza o valor total do pedido
                $this->atualizarValorTotal($idPedido);
                return true;
            }

            return false;
        } catch (Exception $e) {
            log_message('error', "Erro ao adicionar item ao pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza o valor total do pedido somando todos os itens
     *
     * @param int $idPedido ID do pedido
     * @return bool Sucesso ou falha
     */
    protected function atualizarValorTotal(int $idPedido): bool
    {
        try {
            // Calcula o total dos itens
            $builder = $this->db->table('pedidos_itens');
            $builder->selectSum('subtotal');
            $builder->where('id_pedido', $idPedido);
            $query = $builder->get();
            $result = $query->getRow();

            $valorTotal = $result->subtotal ?? 0;

            // Busca o pedido para obter valor do frete e desconto
            $pedido = $this->db->table('pedidos')
                ->where('id_pedido', $idPedido)
                ->get()
                ->getRowArray();

            if ($pedido) {
                // Adiciona o frete e subtrai o desconto
                $valorTotal += $pedido['valor_frete'];
                $valorTotal -= $pedido['valor_desconto'];

                // Atualiza o valor total
                $this->update($idPedido, ['valor_total' => $valorTotal]);
                return true;
            }

            return false;
        } catch (Exception $e) {
            log_message('error', "Erro ao atualizar valor total do pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza o status de um pedido
     *
     * @param int $idPedido ID do pedido
     * @param string $status Novo status
     * @return bool Sucesso ou falha
     */
    public function atualizarStatus(int $idPedido, string $status): bool
    {
        try {
            return $this->db->table('pedidos')
                ->where('id', $idPedido)
                ->update([
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        } catch (Exception $e) {
            log_message('error', "Erro ao atualizar status do pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove um item do pedido
     *
     * @param int $idPedido ID do pedido
     * @param int $idItem ID do item
     * @return bool Sucesso ou falha
     */
    public function removerItem(int $idPedido, int $idItem): bool
    {
        try {
            $builder = $this->db->table('pedidos_itens');
            $builder->where('id_item', $idItem);
            $builder->where('id_pedido', $idPedido);
            $success = $builder->delete();

            if ($success) {
                // Atualiza o valor total do pedido
                $this->atualizarValorTotal($idPedido);
                return true;
            }

            return false;
        } catch (Exception $e) {
            log_message('error', "Erro ao remover item do pedido: " . $e->getMessage());
            return false;
        }
    }
}