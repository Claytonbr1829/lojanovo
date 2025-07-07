<?php

namespace App\Models;

use CodeIgniter\Model;

class EnderecoEntregaModel extends Model
{
    protected $table = 'endereco_entrega';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_cliente',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'municipio',
        'estado',
        'principal',

    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [

        'cep' => 'required|max_length[8]',
        'logradouro' => 'required|max_length[255]',
        'numero' => 'required|max_length[20]',
        'bairro' => 'required|max_length[255]',
        'municipio' => 'required|max_length[255]',
        'estado' => 'required|max_length[255]'
    ];

    protected $validationMessages = [
        'cep' => [
            'required' => 'O CEP é obrigatório',
            'max_length' => 'O CEP deve ter no máximo 8 caracteres'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['logBeforeInsert'];
    protected $afterInsert = ['logAfterInsert'];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function logBeforeInsert(array $dadosEndereco)
    {
        log_message('info', 'Dados antes da inserção: ' . print_r($dadosEndereco, true));
        return $dadosEndereco;
    }

    protected function logAfterInsert(array $dadosEndereco)
    {
        log_message('info', 'Dados após inserção: ' . print_r($dadosEndereco, true));
        if (!$dadosEndereco['result']) {
            log_message('error', 'Erro na inserção: ' . print_r($this->errors(), true));
        }
        return $dadosEndereco;
    }
    public function insert($dadosEndereco = null, bool $returnID = true)
    {
        log_message('info', 'Tentando inserir endereço: ' . print_r($dadosEndereco, true));

        $result = parent::insert($dadosEndereco, $returnID);

        if (!$result) {
            $errors = $this->errors();
            log_message('error', 'Erro ao inserir endereço: ' . print_r($errors, true));
            // Adicione isso para ver os erros imediatamente
            die(print_r($errors, true));
        }

        return $result; // Remova o dd() para não interromper o fluxo
    }

    // No arquivo app/Models/EnderecoEntregaModel.php
    // EnderecoEntregaModel
    public function enderecoExiste($dadosEndereco)
    {
        return $this->where('id_cliente', $dadosEndereco['id_cliente'])
            ->where('cep', $dadosEndereco['cep'])
            ->where('logradouro', $dadosEndereco['logradouro'])
            ->where('numero', $dadosEndereco['numero'])
            ->first(); // retorna array ou null
    }

    public function marcarOutrosComoNaoPrincipal($idCliente, $idEnderecoExcluir = null)
    {
        $builder = $this->where('id_cliente', $idCliente);

        if ($idEnderecoExcluir) {
            $builder->where('id !=', $idEnderecoExcluir);
        }

        return $builder->set(['principal' => 0])->update();
    }
}
