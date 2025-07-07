<?php
namespace App\Models;

use PDO;
use Exception;

class MarcaParceiraModel extends MarcaParceiraModelBase
{
    private $modelConsulta;
    private $modelDados;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->modelConsulta = new MarcaParceiraModelConsulta();
        $this->modelDados = new MarcaParceiraModelDados();
    }
    
    /**
     * Obtém as marcas parceiras ativas para a loja virtual
     *
     * @param int $limit Limite de marcas a retornar
     * @return array Lista de marcas parceiras
     */
    public function getMarcasParceiras(int $limit = 6): array
    {
        return $this->modelConsulta->getMarcasParceiras($limit);
    }
    
    /**
     * Obtém uma marca parceira específica pelo ID
     *
     * @param int $id ID da marca parceira
     * @return array|null Dados da marca parceira ou null se não encontrada
     */
    public function getMarcaParceira(int $id): ?array
    {
        return $this->modelConsulta->getMarcaParceira($id);
    }
    
    /**
     * Insere uma nova marca parceira no banco de dados
     *
     * @param array $dados Dados da marca parceira
     * @return bool Resultado da operação
     */
    public function inserirMarcaParceira(array $dados): bool
    {
        return $this->modelDados->inserirMarcaParceira($dados);
    }
    
    /**
     * Atualiza uma marca parceira existente
     *
     * @param int $id ID da marca parceira
     * @param array $dados Dados atualizados
     * @return bool Resultado da operação
     */
    public function atualizarMarcaParceira(int $id, array $dados): bool
    {
        return $this->modelDados->atualizarMarcaParceira($id, $dados);
    }
    
    /**
     * Exclui uma marca parceira
     *
     * @param int $id ID da marca parceira
     * @return bool Resultado da operação
     */
    public function excluirMarcaParceira(int $id): bool
    {
        return $this->modelDados->excluirMarcaParceira($id);
    }
} 