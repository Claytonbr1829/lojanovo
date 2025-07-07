<?php
namespace App\Models;

use PDO;
use Exception;

/**
 * Modelo principal para produtos, que integra todas as funcionalidades
 * divididas em arquivos separados para melhor organização.
 */
class ProdutoModel extends ProdutoModelBase
{
    // Inclui classes de funcionalidades específicas
    private $modelHome;
    private $modelDetalhe;
    private $modelCategoria;
    private $modelListagem;
    private $modelRelacionados;
    private $modelUtilidades;
    private $modelDetalhes;
    private $modelCRUD;
    
    public function __construct()
    {
        parent::__construct();
        
        // Instancia os modelos específicos
        $this->modelHome = new ProdutoModelHome();
        $this->modelDetalhe = new ProdutoModelDetalhe();
        $this->modelCategoria = new ProdutoModelCategoria();
        $this->modelListagem = new ProdutoModelListagem();
        $this->modelRelacionados = new ProdutoModelRelacionados();
        $this->modelUtilidades = new ProdutoModelUtilidades();
        $this->modelDetalhes = new ProdutoModelDetalhes();
        $this->modelCRUD = new ProdutoModelCRUD();
    }
    
    // Métodos ProdutoModelHome
    
    /**
     * Obtém produtos em destaque para a página inicial
     */
    public function getProdutosDestaque(int $limit = 8): array
    {
        return $this->modelHome->getProdutosDestaque($limit);
    }
    
    /**
     * Obtém os produtos mais vendidos
     */
    public function getProdutosMaisVendidos(int $limit = 4): array
    {
        return $this->modelHome->getProdutosMaisVendidos($limit);
    }
    
    /**
     * Obtém os produtos mais recentes
     */
    public function getProdutosNovos(int $limit = 4): array
    {
        return $this->modelHome->getProdutosNovos($limit);
    }
    
    // Métodos ProdutoModelDetalhe
    
    /**
     * Obtém um produto específico pelo ID
     */
    public function getProduto($id) :?array
    {
        return $this->modelDetalhe->getProduto($id);
    }
    
    /**
     * Obtém um produto pelo seu slug
     */
    public function getProdutoBySlug(string $slug): ?array
    {
        return $this->modelDetalhe->getProdutoBySlug($slug);
    }
    
    // Métodos ProdutoModelCategoria
    
    /**
     * Obtém produtos de uma categoria específica
     */
    public function getProdutosByCategoria($id_categoria, $limit = 12, $offset = 0, $sortBy = 'nome', $sortOrder = 'asc'): array
    {
        return $this->modelCategoria->getProdutosByCategoria($id_categoria, $limit, $offset, $sortBy, $sortOrder);
    }
    
    /**
     * Conta o total de produtos em uma categoria
     */
    public function countProdutosByCategoria($id_categoria): int
    {
        return $this->modelCategoria->countProdutosByCategoria($id_categoria);
    }
    
    // Métodos ProdutoModelListagem
    
    /**
     * Busca produtos com filtros, ordenação e paginação
     */
    public function getProdutos(
        int $limit = 12,
        int $offset = 0,
        string $sortBy = 'nome',
        string $sortOrder = 'asc',
        ?int $categoria = null,
        ?float $precoMin = null,
        ?float $precoMax = null,
        ?string $busca = null
    ): array {
        return $this->modelListagem->getProdutos($limit, $offset, $sortBy, $sortOrder, $categoria, $precoMin, $precoMax, $busca);
    }
    
    /**
     * Conta o total de produtos com os filtros aplicados
     */
    public function countProdutos(
        ?int $categoria = null,
        ?float $precoMin = null,
        ?float $precoMax = null,
        ?string $busca = null
    ): int {
        return $this->modelListagem->countProdutos($categoria, $precoMin, $precoMax, $busca);
    }
    
    /**
     * Obtém produtos da loja sem filtros avançados
     */
    public function getProdutosLoja(int $limit = 12, int $offset = 0): array
    {
        return $this->modelListagem->getProdutosLoja($limit, $offset);
    }
    
    // Métodos ProdutoModelRelacionados
    
    /**
     * Obtém produtos relacionados (da mesma categoria)
     */
    public function getProdutosRelacionados(int $idProduto, int $idCategoria, int $limit = 4): array
    {
        return $this->modelRelacionados->getProdutosRelacionados($idProduto, $idCategoria, $limit);
    }
    
    /**
     * Busca produtos relacionados a um produto (versão completa)
     */
    public function getProdutosRelacionadosCompleto(int $id_produto, int $id_categoria, int $limit = 4): array
    {
        return $this->modelRelacionados->getProdutosRelacionadosCompleto($id_produto, $id_categoria, $limit);
    }
    
    // Métodos ProdutoModelUtilidades
    
    /**
     * Incrementa o contador de visualizações de um produto
     */
    public function incrementarVisualizacoes(int $id_produto): bool
    {
        return $this->modelUtilidades->incrementarVisualizacoes($id_produto);
    }
    
    /**
     * Busca imagens adicionais de um produto
     */
    public function getImagensProduto(int $id_produto): array
    {
        return $this->modelUtilidades->getImagensProduto($id_produto);
    }
    
    /**
     * Verifica se produto está disponível em estoque
     */
    public function verificarEstoque(int $id_produto, int $quantidade = 1): bool
    {
        return $this->modelUtilidades->verificarEstoque($id_produto, $quantidade);
    }
    
    /**
     * Atualiza o estoque após uma venda
     */
    public function atualizarEstoque(int $id_produto, int $quantidade): bool
    {
        return $this->modelUtilidades->atualizarEstoque($id_produto, $quantidade);
    }
    
    /**
     * Operações de Detalhes
     */
    
    /**
     * Busca um produto pelo ID com suas informações detalhadas
     */
    public function getById(int $id): ?array
    {
        return $this->modelDetalhes->getById($id);
    }
    
    /**
     * Busca um produto pelo slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->modelDetalhes->getBySlug($slug);
    }
    
    /**
     * Obtém as imagens adicionais de um produto
     */
    /* Método duplicado - removido para evitar conflitos
    public function getImagensProduto(int $idProduto): array
    {
        return $this->modelDetalhes->getImagensProduto($idProduto);
    }
    */
    
    /**
     * Obtém as variações de um produto
     */
    public function getVariacoesProduto(int $idProduto): array
    {
        return $this->modelDetalhes->getVariacoesProduto($idProduto);
    }
    
    /**
     * Obtém os atributos de um produto
     */
    public function getAtributosProduto(int $idProduto): array
    {
        return $this->modelDetalhes->getAtributosProduto($idProduto);
    }
    
    /**
     * Operações de CRUD
     */
    
    /**
     * Cria um novo produto
     */
    public function criar(array $dados): int
    {
        return $this->modelCRUD->criar($dados);
    }
    
    /**
     * Atualiza um produto existente
     */
    public function atualizar(int $id, array $dados): bool
    {
        return $this->modelCRUD->atualizar($id, $dados);
    }
    
    /**
     * Remove um produto
     */
    public function excluir(int $id): bool
    {
        return $this->modelCRUD->excluir($id);
    }
    
    /**
     * Adiciona uma imagem ao produto
     */
    public function adicionarImagem(int $idProduto, string $imagem, int $ordem = 0): bool
    {
        return $this->modelCRUD->adicionarImagem($idProduto, $imagem, $ordem);
    }
    
    /**
     * Remove uma imagem do produto
     */
    public function removerImagem(int $idImagem): bool
    {
        return $this->modelCRUD->removerImagem($idImagem);
    }
    
    /**
     * Adiciona um atributo ao produto
     */
    public function adicionarAtributo(int $idProduto, int $idAtributo, string $valor): bool
    {
        return $this->modelCRUD->adicionarAtributo($idProduto, $idAtributo, $valor);
    }
    
    /**
     * Adiciona uma variação ao produto
     */
    public function adicionarVariacao(int $idProduto, array $dados): bool
    {
        return $this->modelCRUD->adicionarVariacao($idProduto, $dados);
    }
    
    /**
     * Busca produtos por termo de pesquisa
     */
    public function buscarProdutos(string $termo, int $limit = 12, int $offset = 0): array
    {
        try {
            $db = \Config\Database::connect();
            
            $query = $db->table('produtos p')
                ->select('p.*, c.nome as categoria')
                ->join('categorias_dos_produtos c', 'c.id_categoria = p.id_categoria', 'left')
                ->like('p.nome', $termo)
                ->orLike('p.descricao', $termo)
                ->orLike('p.codigo_sku', $termo)
                ->orLike('p.codigo_de_barras', $termo)
                ->orLike('c.nome', $termo)
                ->where('p.ativo', 1)
                ->orderBy('p.nome', 'ASC')
                ->limit($limit, $offset)
                ->get();
                
            return $query->getResultArray();
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar produtos: ' . $e->getMessage());
            return [];
        }
    }

    public function findPedidoItens($id): array
    {
        try {
            $db = \Config\Database::connect();
            
            $query = $db->table('pedidos_itens')
                ->select('pedidos_itens.*, produtos.*')
                ->join('produtos', 'produtos.id_produto = pedidos_itens.id_produto', 'left')
                ->where('pedidos_itens.id_pedido', $id)
                ->get();

            return $query->getResultArray();
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar produtos: ' . $e->getMessage());
            return [];
        }
    }
} 