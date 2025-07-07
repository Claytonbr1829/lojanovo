<?php
namespace App\Models;
use CodeIgniter\Model;

class ClienteModelBase extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $returnType = 'object';
    protected $allowedFields = [
        'tipo', 'nome', 'cpf', 'razao_social', 'nome_fantasia', 'cnpj', 
        'ie', 'im', 'isento', 'rg', 'data_de_nascimento', 'email', 
        'senha_novo', 'celular_1', 'celular_2', 'fixo', 'cep', 'logradouro', 
        'numero', 'complemento', 'bairro', 'id_uf', 'id_municipio', // Adicionei 'municipio' aqui
        'responsavel', 'id_empresa', 'status', 'id_gateway', 'senha', 'login'     
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;

    // Regras de validação
    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|is_unique[clientes.email,id_cliente,{id_cliente}]',
        'tipo' => 'required|in_list[1,2]', // 1 = Pessoa Física, 2 = Pessoa Jurídica
        'status' => 'required|in_list[0,1]' // 0 = Inativo, 1 = Ativo
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório',
            'min_length' => 'O Nome deve ter pelo menos 3 caracteres',
            'max_length' => 'O Nome deve ter no máximo 100 caracteres'
        ],
        'email' => [
            'required' => 'O campo E-mail é obrigatório',
            'valid_email' => 'O E-mail informado não é válido',
            'is_unique' => 'Este E-mail já está sendo utilizado'
        ],
        'tipo' => [
            'required' => 'O campo Tipo de Pessoa é obrigatório',
            'in_list' => 'O Tipo de Pessoa deve ser Física ou Jurídica'
        ],
        'status' => [
            'required' => 'O campo Status é obrigatório',
            'in_list' => 'O Status deve ser Ativo ou Inativo'
        ]
    ];

    // Adicione isso para debug
    public function afterUpdate(array $data)
    {
        log_message('debug', 'Dados após atualização: ' . print_r($data, true));
        return $data;
    }
}