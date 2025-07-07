<?php
namespace App\Models;

class ProdutoModelUtilidades extends ProdutoModelBase
{
    /**
     * Incrementa o contador de visualizações de um produto
     *
     * @param int $id_produto ID do produto
     * @return bool
     */
    public function incrementarVisualizacoes(int $id_produto): bool
    {
        try {
            $this->set('visualizacoes', 'visualizacoes + 1', false)
                ->where('id_produto', $id_produto)
                ->update();
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao incrementar visualizações: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca imagens adicionais de um produto
     *
     * @param int $id_produto ID do produto
     * @return array
     */
    public function getImagensProduto(int $id_produto): array
    {
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('loja_produtos_imagens');
            
            $imagens = $builder->select('id_imagem, arquivo, ordem')
                ->where('id_produto', $id_produto)
                ->orderBy('ordem', 'ASC')
                ->get()
                ->getResultArray();
            
            // Garante que todas as imagens existem ou define imagem padrão
            foreach ($imagens as &$imagem) {
                if (empty($imagem['arquivo']) || !file_exists(FCPATH . 'uploads/produtos/' . $imagem['arquivo'])) {
                    $imagem['arquivo'] = 'produto-default.jpg';
                }
            }
            
            return $imagens;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar imagens do produto: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verifica se produto está disponível em estoque
     *
     * @param int $id_produto ID do produto
     * @param int $quantidade Quantidade desejada
     * @return bool
     */
    public function verificarEstoque(int $id_produto, int $quantidade = 1): bool
    {
        try {
            $produto = $this->select('quantidade')
                ->where('id_produto', $id_produto)
                ->where('ativo', 1)
                ->get()
                ->getRowArray();
                
            if (!$produto) {
                return false;
            }
            
            return (int)$produto['quantidade'] >= $quantidade;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao verificar estoque: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualiza o estoque após uma venda
     *
     * @param int $id_produto ID do produto
     * @param int $quantidade Quantidade vendida
     * @return bool
     */
    public function atualizarEstoque(int $id_produto, int $quantidade): bool
    {
        try {
            $this->set('quantidade', 'quantidade - ' . (int)$quantidade, false)
                ->where('id_produto', $id_produto)
                ->where('quantidade >=', $quantidade)
                ->update();
                
            return $this->db->affectedRows() > 0;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar estoque: ' . $e->getMessage());
            return false;
        }
    }
} 