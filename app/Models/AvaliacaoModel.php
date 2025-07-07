<?php

namespace App\Models;

use CodeIgniter\Model;

class AvaliacaoModel extends Model
{
    protected $table            = 'loja_avaliacoes';
    protected $primaryKey       = 'id_avaliacao';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_produto', 
        'id_usuario', 
        'nome', 
        'email', 
        'avaliacao', 
        'comentario', 
        'data_avaliacao', 
        'status', 
        'ip'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'data_avaliacao';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules = [
        'id_produto'     => 'required|integer',
        'nome'           => 'required|min_length[3]|max_length[100]',
        'email'          => 'required|valid_email|max_length[100]',
        'avaliacao'      => 'required|integer|greater_than[0]|less_than_equal_to[5]',
        'comentario'     => 'required|min_length[10]|max_length[500]',
    ];
    
    protected $validationMessages = [
        'id_produto' => [
            'required' => 'O ID do produto é obrigatório.',
            'integer' => 'O ID do produto deve ser um número inteiro.'
        ],
        'nome' => [
            'required' => 'O nome é obrigatório.',
            'min_length' => 'O nome deve ter pelo menos {param} caracteres.',
            'max_length' => 'O nome não pode exceder {param} caracteres.'
        ],
        'email' => [
            'required' => 'O e-mail é obrigatório.',
            'valid_email' => 'Por favor, informe um e-mail válido.',
            'max_length' => 'O e-mail não pode exceder {param} caracteres.'
        ],
        'avaliacao' => [
            'required' => 'A avaliação é obrigatória.',
            'integer' => 'A avaliação deve ser um número inteiro.',
            'greater_than' => 'A avaliação deve ser maior que {param}.',
            'less_than_equal_to' => 'A avaliação deve ser menor ou igual a {param}.'
        ],
        'comentario' => [
            'required' => 'O comentário é obrigatório.',
            'min_length' => 'O comentário deve ter pelo menos {param} caracteres.',
            'max_length' => 'O comentário não pode exceder {param} caracteres.'
        ]
    ];

    /**
     * Obtém as avaliações de um produto específico
     *
     * @param int $id_produto ID do produto
     * @param int $limit Limite de avaliações
     * @param int $offset Deslocamento para paginação
     * @param bool $apenasAprovadas Se deve retornar apenas avaliações aprovadas
     * @return array
     */
    public function getAvaliacoesProduto(int $id_produto, int $limit = 10, int $offset = 0, bool $apenasAprovadas = true): array
    {
        try {
            $builder = $this->select('*')
                ->where('id_produto', $id_produto);
            
            if ($apenasAprovadas) {
                $builder->where('status', 'A'); // A = Aprovado
            }
            
            return $builder->orderBy('data_avaliacao', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar avaliações: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém a média de avaliações de um produto específico
     *
     * @param int $id_produto ID do produto
     * @param bool $apenasAprovadas Se deve considerar apenas avaliações aprovadas
     * @return float
     */
    public function getMediaAvaliacoesProduto(int $id_produto, bool $apenasAprovadas = true): float
    {
        try {
            $builder = $this->selectAvg('avaliacao', 'media')
                ->where('id_produto', $id_produto);
            
            if ($apenasAprovadas) {
                $builder->where('status', 'A'); // A = Aprovado
            }
            
            $result = $builder->get()->getRowArray();
            
            if (isset($result['media'])) {
                return round((float)$result['media'], 1);
            }
            
            return 0.0;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao calcular média de avaliações: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Conta o número de avaliações para um produto
     *
     * @param int $id_produto ID do produto
     * @param bool $apenasAprovadas Se deve considerar apenas avaliações aprovadas
     * @return int
     */
    public function countAvaliacoesProduto(int $id_produto, bool $apenasAprovadas = true): int
    {
        try {
            $builder = $this->where('id_produto', $id_produto);
            
            if ($apenasAprovadas) {
                $builder->where('status', 'A'); // A = Aprovado
            }
            
            return $builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Erro ao contar avaliações: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Aprova uma avaliação
     *
     * @param int $id_avaliacao ID da avaliação
     * @return bool
     */
    public function aprovarAvaliacao(int $id_avaliacao): bool
    {
        try {
            return $this->update($id_avaliacao, ['status' => 'A']);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao aprovar avaliação: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Rejeita uma avaliação
     *
     * @param int $id_avaliacao ID da avaliação
     * @return bool
     */
    public function rejeitarAvaliacao(int $id_avaliacao): bool
    {
        try {
            return $this->update($id_avaliacao, ['status' => 'R']);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao rejeitar avaliação: ' . $e->getMessage());
            return false;
        }
    }
} 