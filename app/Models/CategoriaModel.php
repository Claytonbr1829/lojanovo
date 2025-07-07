<?php
namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    public $idEmpresa = 0;

    protected $table         = 'categorias_dos_produtos';
    protected $primaryKey    = 'id_categoria';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id_empresa', 'nome', 'slug', 'descricao', 'imagem', 
        'icone', 'ordem', 'ativo', 'destaque', 'id_categoria_pai'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'data_criacao';
    protected $updatedField  = 'data_atualizacao';
    
    protected $validationRules = [
        'id_empresa'  => 'required|integer',
        'nome'        => 'required|string|min_length[2]|max_length[100]',
        'slug'        => 'permit_empty|string|max_length[100]',
        'descricao'   => 'permit_empty|string',
        'imagem'      => 'permit_empty|string|max_length[255]',
        'icone'       => 'permit_empty|string|max_length[50]',
        'ordem'       => 'permit_empty|integer',
        'ativo'       => 'permit_empty|integer|in_list[0,1]',
        'destaque'    => 'permit_empty|integer|in_list[0,1]',
        'id_categoria_pai' => 'permit_empty|integer'
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome da categoria é obrigatório',
            'min_length' => 'O nome da categoria deve ter pelo menos 2 caracteres',
            'max_length' => 'O nome da categoria não pode exceder 100 caracteres'
        ]
    ];
    
    private $modelLista;
    private $modelDetalhe;
    
    public function __construct()
    {
        parent::__construct();
        $this->modelLista = new CategoriaModelLista();
        $this->modelDetalhe = new CategoriaModelDetalhe();
    }
    
    /**
     * Métodos de listagem de categorias
     */
    
    /**
     * Obtém todas as categorias ativas para a loja virtual, filtrando por empresa
     *
     * @param int $limit Limite de registros (0 = sem limite)
     * @param bool $apenasDestaque Buscar apenas categorias em destaque
     * @param int|null $idEmpresa ID da empresa (null = usa o ID atual)
     * @return array Lista de categorias
     */
    public function getCategorias(int $limit = 0, bool $apenasDestaque = false, ?int $idEmpresa = null): array
    {
        // Se não foi informado, usa o ID da empresa atual
        if ($idEmpresa === null) {
            $idEmpresa = $this->idEmpresa ?? 0;
        }
        
        return $this->modelLista->getCategorias($limit, $apenasDestaque, $idEmpresa);
    }
    
    /**
     * Obtém as categorias para exibição no menu, filtrando por empresa
     * 
     * @param int $limit Limite de categorias (0 = sem limite)
     * @param int|null $idEmpresa ID da empresa (null = usa o ID atual)
     * @return array Lista de categorias para o menu
     */
    public function getCategoriasMenu(int $limit = 0, ?int $idEmpresa = null): array
    {
        // Se não foi informado, usa o ID da empresa atual
        if ($idEmpresa === null) {
            $idEmpresa = $this->idEmpresa ?? 0;
        }
        
        return $this->modelLista->getCategoriasMenu($limit, $idEmpresa);
    }
    
    /**
     * Obtém categorias em destaque para a loja virtual, filtrando por empresa
     *
     * @param int $limit Limite de registros
     * @param int|null $idEmpresa ID da empresa (null = usa o ID atual)
     * @return array Lista de categorias em destaque
     */
    public function getCategoriasDestaque(int $limit = 4, ?int $idEmpresa = null): array
    {
        // Se não foi informado, usa o ID da empresa atual
        if ($idEmpresa === null) {
            $idEmpresa = $this->idEmpresa ?? 0;
        }
        
        return $this->modelLista->getCategoriasDestaque($limit, $idEmpresa);
    }
    
    public function contarProdutosPorCategoria(): array
    {
        return $this->modelLista->contarProdutosPorCategoria();
    }
    
    /**
     * Métodos de detalhes de categorias
     */
    
    public function getCategoria(int $id): ?array
    {
        return $this->modelDetalhe->getCategoria($id);
    }
    
    public function getCategoriaBySlug(string $slug): ?array
    {
        return $this->modelDetalhe->getCategoriaBySlug($slug);
    }
    
    public function getSubcategorias(int $idCategoriaPai): array
    {
        return $this->modelDetalhe->getSubcategorias($idCategoriaPai);
    }
    
    public function contarProdutos(int $idCategoria): int
    {
        return $this->modelDetalhe->contarProdutos($idCategoria);
    }
} 