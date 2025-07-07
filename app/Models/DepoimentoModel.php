<?php
namespace App\Models;

use PDO;
use Exception;

class DepoimentoModel extends DepoimentoModelBase
{
    private $modelConsulta;
    private $modelDados;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->modelConsulta = new DepoimentoModelConsulta();
        $this->modelDados = new DepoimentoModelDados();
    }
    
    /**
     * Obtém os depoimentos ativos para a loja virtual
     *
     * @param int $limit Limite de depoimentos a retornar
     * @return array Lista de depoimentos
     */
    public function getDepoimentos(int $limit = 3): array
    {
        return $this->modelConsulta->getDepoimentos($limit);
    }
    
    /**
     * Obtém um depoimento específico pelo ID
     *
     * @param int $id ID do depoimento
     * @return array|null Dados do depoimento ou null se não encontrado
     */
    public function getDepoimento(int $id): ?array
    {
        return $this->modelConsulta->getDepoimento($id);
    }
    
    /**
     * Insere um novo depoimento no banco de dados
     *
     * @param array $dados Dados do depoimento
     * @return bool Resultado da operação
     */
    public function inserirDepoimento(array $dados): bool
    {
        return $this->modelDados->inserirDepoimento($dados);
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
        return $this->modelDados->atualizarDepoimento($id, $dados);
    }
    
    /**
     * Exclui um depoimento
     *
     * @param int $id ID do depoimento
     * @return bool Resultado da operação
     */
    public function excluirDepoimento(int $id): bool
    {
        return $this->modelDados->excluirDepoimento($id);
    }
} 