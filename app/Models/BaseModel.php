<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Config\Services;

/**
 * Modelo base para todos os modelos da aplicação
 * 
 * Este modelo estende o Model padrão do CodeIgniter e adiciona
 * funcionalidades comuns, como o ID da empresa.
 */
class BaseModel extends Model
{
    protected $idEmpresa;
    protected $db;
    protected $builder;

    /**
     * Construtor do BaseModel
     * 
     * Inicializa a conexão com o banco de dados e configura o ID da empresa
     * 
     * @param ConnectionInterface|null $db
     * @param int|null $empresaId
     */
    public function __construct(ConnectionInterface $db = null, int $empresaId = null)
    {
        parent::__construct($db);
        
        // Obtém a conexão com o banco de dados
        $this->db = \Config\Database::connect();
        
        // Inicializa o builder se a tabela foi definida
        if (!empty($this->table)) {
            $this->builder = $this->db->table($this->table);
        }
        
        // Se foi fornecido um ID específico, usa-o
        if ($empresaId !== null) {
            $this->idEmpresa = $empresaId;
            return;
        }
        
        // Obtém o ID da empresa da sessão, se existir
        $session = \Config\Services::session();
        if ($session->has('empresa_id')) {
            $this->idEmpresa = $session->get('empresa_id');
        } else {
            // Se não houver ID na sessão, define como null para indicar que
            // não foi encontrada empresa correspondente
            $this->idEmpresa = null;
        }
    }

    /**
     * Obtém o ID da empresa atual
     *
     * @return int ID da empresa
     */
    public function getIdEmpresa(): int
    {
        return $this->idEmpresa;
    }

    /**
     * Define o ID da empresa
     *
     * @param int $idEmpresa ID da empresa
     * @return void
     */
    public function setIdEmpresa(int $idEmpresa): void
    {
        $this->idEmpresa = $idEmpresa;
    }

    /**
     * Executar consulta personalizada
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parâmetros da consulta
     * @param bool $single Se deve retornar uma única linha
     * @return array|object Resultado da consulta
     */
    public function executeQuery(string $sql, array $params = [], bool $single = false)
    {
        $query = $this->db->query($sql, $params);
        
        if ($single) {
            return $query->getRow();
        }
        
        return $query->getResult();
    }

    /**
     * Seleciona colunas específicas
     */
    public function select(string $select): self
    {
        $this->builder->select($select);
        return $this;
    }

    /**
     * Define a tabela para consulta
     */
    public function from(string $table): self
    {
        $this->builder->from($table);
        return $this;
    }

    /**
     * Adiciona uma condição WHERE
     */
    public function where($key, $value = null, bool $escape = null): self
    {
        $this->builder->where($key, $value, $escape);
        return $this;
    }

    /**
     * Adiciona uma junção de tabela
     */
    public function join(string $table, string $condition, string $type = ''): self
    {
        $this->builder->join($table, $condition, $type);
        return $this;
    }

    /**
     * Define ordenação
     */
    public function orderBy(string $orderBy, string $direction = ''): self
    {
        $this->builder->orderBy($orderBy, $direction);
        return $this;
    }

    /**
     * Define limite
     */
    public function limit(int $limit, ?int $offset = 0): self
    {
        $this->builder->limit($limit, $offset);
        return $this;
    }

    /**
     * Executa a consulta e retorna os resultados
     */
    public function get()
    {
        return $this->builder->get();
    }

    /**
     * Conta os resultados da consulta
     */
    public function count(): int
    {
        return $this->builder->countAllResults();
    }

    /**
     * Conta os resultados da consulta, mas reseta o builder para poder continuar usando
     */
    public function countAllResults(bool $reset = true, bool $test = false): int
    {
        return $this->builder->countAllResults($reset);
    }

    /**
     * Modificado para garantir aplicação do filtro por empresa em todas as consultas
     * 
     * @param array $data
     * @return array|object
     */
    public function findAll(?int $limit = null, int $offset = 0): array
    {
        // Filtro por empresa se aplicável
        if ($this->shouldFilterByCompany()) {
            $this->where($this->table . '.id_empresa', $this->idEmpresa);
        }
        
        // Se limite for null ou 0, retorna todos os registros
        if ($limit === null || $limit === 0) {
            return parent::findAll();
        }
        
        return parent::findAll($limit, $offset);
    }
    
    /**
     * Verifica se deve filtrar por empresa
     */
    protected function shouldFilterByCompany(): bool
    {
        return !empty($this->idEmpresa) && 
               property_exists($this, 'table') && 
               $this->db->fieldExists('id_empresa', $this->table);
    }
    
    /**
     * Modificado para garantir aplicação do filtro por empresa
     * 
     * @param array $id
     * @return array|object|null
     */
    public function find($id = null)
    {
        if ($this->idEmpresa && property_exists($this, 'table') && $this->hasColumn('id_empresa')) {
            $this->where($this->table . '.id_empresa', $this->idEmpresa);
        }
        
        return parent::find($id);
    }
    
    /**
     * Modificado para garantir aplicação do filtro por empresa
     * 
     * @param array $constraints
     * @return array|object
     */
    public function findColumn(string $columnName)
    {
        if ($this->idEmpresa && property_exists($this, 'table') && $this->hasColumn('id_empresa')) {
            $this->where($this->table . '.id_empresa', $this->idEmpresa);
        }
        
        return parent::findColumn($columnName);
    }
    
    /**
     * Modificado para garantir aplicação do filtro por empresa
     * 
     * @param string|null $table Nome da tabela opcional
     * @return \CodeIgniter\Database\BaseBuilder
     */
    public function builder(?string $table = null)
    {
        $builder = parent::builder($table);
        
        // Se uma tabela específica foi fornecida, use-a
        $tableName = $table ?? $this->table;
        
        if ($this->idEmpresa && $tableName && $this->hasColumn('id_empresa', $tableName)) {
            $builder->where($tableName . '.id_empresa', $this->idEmpresa);
        }
        
        return $builder;
    }
    
    /**
     * Verifica se a tabela possui a coluna informada
     * 
     * @param string $column Nome da coluna
     * @param string|null $tableName Nome da tabela (opcional)
     * @return bool
     */
    protected function hasColumn(string $column, ?string $tableName = null): bool
    {
        $db = db_connect();
        $table = $tableName ?? $this->table;
        $fields = $db->getFieldData($table);
        
        foreach ($fields as $field) {
            if ($field->name === $column) {
                return true;
            }
        }
        
        return false;
    }
} 