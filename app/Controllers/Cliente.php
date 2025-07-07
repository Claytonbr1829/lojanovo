<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\UfModel;
use App\Models\MunicipioModel;
use App\Models\PedidoModel;
use CodeIgniter\HTTP\ResponseInterface;

class Cliente extends BaseController
{
    protected $clienteModel;
    protected $ufModel;
    protected $municipioModel;
    protected $pedidoModel;
    protected $db;

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
        $this->ufModel = new UfModel();
        $this->municipioModel = new MunicipioModel();
        $this->pedidoModel = new PedidoModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Exibe a tela de login
     */
    public function login()
    {
        $data = [];

        // Verifica se há mensagem de erro
        if (session()->has('error')) {
            $data['error'] = session()->getFlashdata('error');
        }

        // Verifica se há mensagem de sucesso
        if (session()->has('success')) {
            $data['success'] = session()->getFlashdata('success');
        }

        // Renderiza a view de login
        return $this->renderView('/cliente/login', $data);
    }

    /**
     * Exibe a tela de cadastro
     */
    public function cadastro()
    {
        // Carrega os estados para o formulário
        $estados = $this->ufModel->getAll();

        $data = [
            'estados' => $estados
        ];

        // Verifica se há mensagem de erro
        if (session()->has('error')) {
            $data['error'] = session()->getFlashdata('error');
        }

        // Renderiza a view de cadastro
        return $this->renderView('cadastro', $data);
    }

    /**
     * Processa o login do cliente
     */
    public function autenticar()
    {
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');
        $lembrar = (bool) $this->request->getPost('lembrar') ? true : false;


        // Validação básica
        if (empty($email) || empty($senha)) {
            return redirect()->to('/login')
                ->with('error', 'Por favor, preencha todos os campos.');
        }

        //$clienteModel = new ClienteModel();

        $cliente = $this->clienteModel
            ->where('email', $email)
            ->where('status', 1)
            //->where('id_empresa', $this->idEmpresa)
            ->first();


        // Verifica as credenciais
        $clienteM = $this->clienteModel->autenticar($senha, $cliente);


        // Método mais robusto para conversão
        $clienteData = json_decode(json_encode($clienteM), true);

        // Obtenha a instância do serviço de sessão
        $session = \Config\Services::session();

        if ($clienteData) {
            // Login bem-sucedido
            $clienteLogin = [
                'id' => (int) $clienteData['id_cliente'],
                'nome' => $clienteData['tipo'] == 1 ? $clienteData['nome'] : $clienteData['razao_social'],
                'email' => (string) $clienteData['email'],
                'tipo' => (int) $clienteData['tipo'],
                'cpf' => $clienteData['tipo'] == 1 ? $clienteData['cpf'] : '',
                'cnpj' => $clienteData['tipo'] == 2 ? $clienteData['cnpj'] : '',
                'telefone' => (string) $clienteData['celular_1'],
                'idEmpresa' => $clienteData['id_empresa'],
                'idMunicipio' => $clienteData['id_municipio'],
                'logged_in' => true
            ];


            // Verificação final dos dados
            if (!is_array($clienteLogin)) {
                throw new \RuntimeException('Falha ao converter dados do cliente para array');
            }

            // Armazenar na sessão
            $session->set('cliente', $clienteLogin);

            // Verifique imediatamente
            if (!$session->has('cliente')) {
                log_message('error', 'Falha ao gravar na sessão');
                throw new \RuntimeException('Erro na sessão');
            }

            // Se marcou "lembrar", cria um cookie
            if ($lembrar) {
                $token = bin2hex(random_bytes(32));
                $expira = time() + (30 * 24 * 60 * 60); // 30 dias

                // Salva o token no banco de dados
                $this->clienteModel->salvarToken($clienteData['id_cliente'], $token, $expira);

                // Cria o cookie
                setcookie('cliente_token', $token, $expira, '/', '', false, true);
            }

            // Redireciona para a página inicial ou para o checkout se estiver no processo de compra
            if ($session->has('redirect_after_login')) {
                $redirect = $session->get('redirect_after_login');
                $session->remove('redirect_after_login');
                return redirect()->to($redirect);
            }

            return redirect()->to('/')
                ->with('sucess', 'Login realizado com sucesso!');
        }

        // Login falhou
        return redirect()->to('/login')
            ->with('error', 'E-mail ou senha inválidos.');
    }

    /**
     * Processa o cadastro do cliente
     */
    public function salvar()
    {
        $dados = $this->request->getPost();

        // Validação dos campos obrigatórios
        if (
            empty($dados['email']) || empty($dados['senha']) ||
            empty($dados['confirma_senha']) || empty($dados['id_empresa'])
        ) {
            return redirect()->to('/cadastro')
                ->with('error', 'Por favor, preencha todos os campos obrigatórios.');
        }

        if ($dados['senha'] !== $dados['confirma_senha']) {
            return redirect()->to('/cadastro')
                ->with('error', 'As senhas não conferem.');
        }

        if ($this->clienteModel->emailExiste($dados['email'])) {
            return redirect()->to('/cadastro')
                ->with('error', 'Este e-mail já está cadastrado.');
        }

        // Cria o hash da senha antes de salvar
        $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);

        // Remove o campo de confirmação que não vai para o banco
        unset($dados['confirma_senha']);

        $resultado = $this->clienteModel->salvar($dados);

        if ($resultado > 0) {
            return redirect()->to('/login')
                ->with('success', 'Cadastro realizado com sucesso! Faça login para continuar.');
        } else {
            $error = \Config\Database::connect()->error();
            return redirect()->to('/cadastro')
                ->with('error', 'Erro ao cadastrar: ' . ($error['message'] ?? 'Erro desconhecido'));
        }
    }

    /**
     * Realiza o logout do cliente
     */
    public function logout()
    {
        // Remove o cookie do cliente se existir
        if (isset($_COOKIE['cliente_token'])) {
            setcookie('cliente_token', '', time() - 3600, '/');
        }

        // Limpa os dados da sessão relacionados ao cliente e carrinho
        session()->remove(['cliente', 'carrinho']);

        // Redireciona com mensagem de sucesso
        return redirect()->to('/login')
            ->with('success', 'Logout realizado com sucesso.');
    }

    public function editarEndereco()
    {
        // Carrega dados do cliente da sessão
        $clienteSessao = session()->get('cliente');
        if (!$clienteSessao) {
            return redirect()->to('/login')->with('error', 'Você precisa estar logado');
        }

        $idCliente = is_array($clienteSessao) ? ($clienteSessao['id'] ?? null) : ($clienteSessao->id ?? null);

        // Busca os dados do cliente no banco
        $cliente = (array) $this->clienteModel->find($idCliente);

        $builder = $this->db->table('clientes');
        $builder->select('clientes.*, municipios.municipio as municipio');
        $builder->join('municipios', 'municipios.id_municipio = clientes.id_municipio', 'left');
        $builder->where('clientes.id_cliente', $idCliente);
        $cliente = $builder->get()->getRowArray();

        // Busca os estados do banco
        $ufs = $this->ufModel->findAll();

        return $this->renderView('cadastroC', [
            'cliente' => $cliente,
            'ufs' => $ufs,
            'edicaoEndereco' => true
        ]);
    }

    // No Controller do Cliente (Cliente.php)
    public function salvarEndereco()
    {

        // Obtém os dados do POST
        $dados = $this->request->getPost();

        // Debug: registrar os dados recebidos
        log_message('debug', 'Dados recebidos: ' . print_r($dados, true));

        // Verificação de autenticação
        $clienteSessao = session()->get('cliente');
        if (!$clienteSessao) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Cliente não autenticado'
            ]);
        }

        // Validação dos campos
        $required = [
            'nome',
            'tipo',
            'cpf_cnpj',
            'email',
            'celular_1',
            'data_de_nascimento',
            'cep',
            'logradouro',
            'numero',
            'bairro',
            'municipio',
            'id_uf'
        ];

        foreach ($required as $field) {
            if (empty($dados[$field])) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => "Campo obrigatório faltando: {$field}",
                    'missing' => $field,
                    'received_data' => $dados // Para debug
                ]);
            }
        }

        $municipio = $this->municipioModel
            ->where('municipio', $dados['municipio'])
            ->first();

        if (!$municipio) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Município não encontrado'
            ]);
        }

        $idMunicipio = $municipio['id_municipio']; // ou o nome correto da coluna do ID


        try {
            // Processamento dos dados
            $dadosAtualizacao = [
                'nome' => $dados['nome'],
                'tipo' => $dados['tipo'],
                'email' => $dados['email'],
                'fixo' => preg_replace('/[^0-9]/', '', $dados['fixo'] ?? ''),
                'celular_1' => preg_replace('/[^0-9]/', '', $dados['celular_1']),
                'celular_2' => preg_replace('/[^0-9]/', '', $dados['celular_2'] ?? ''),
                'data_de_nascimento' => $dados['data_de_nascimento'],
                'cep' => preg_replace('/[^0-9]/', '', $dados['cep']),
                'logradouro' => $dados['logradouro'],
                'numero' => $dados['numero'],
                'bairro' => $dados['bairro'],
                'complemento' => $dados['complemento'] ?? null,
                'id_municipio' => $idMunicipio, // Corrigido: usar diretamente do POST
                'id_uf' => $dados['id_uf'], // Corrigido: usar diretamente do POST
                'updated_at' => date('Y-m-d H:i:s')
            ];


            // Tratamento de CPF/CNPJ
            $cpfCnpj = preg_replace('/[^0-9]/', '', $dados['cpf_cnpj']);
            if ($dados['tipo'] == '1') {
                $dadosAtualizacao['cpf'] = $cpfCnpj;
                $dadosAtualizacao['cnpj'] = null;
            } else {
                $dadosAtualizacao['cnpj'] = $cpfCnpj;
                $dadosAtualizacao['cpf'] = null;
            }


            // Atualização no banco de dados
            $db = \Config\Database::connect();
            $builder = $db->table('clientes');
            $cliente = $builder->where('id_cliente', $clienteSessao['id_cliente'] ?? $clienteSessao['id']);
            $result = $builder->update($dadosAtualizacao);

            if (!$result) {
                throw new \RuntimeException('Falha na atualização do cliente');
            }

            // Atualizar sessão
            $builder = $db->table('clientes');
            $clienteAtualizado = $builder
                ->where('id_cliente', $clienteSessao['id_cliente'] ?? $clienteSessao['id'])
                ->get()
                ->getRowArray();

            session()->set('cliente', $clienteAtualizado);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Dados atualizados com sucesso',
                'redirect' => site_url('minha-conta')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro em salvarEndereco: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Erro ao salvar endereço: ' . $e->getMessage()
            ]);
        }
    }

    // Função auxiliar: transforma "SP" em id_uf
    private function buscarIdUF($siglaUf)
    {
        $db = \Config\Database::connect();
        $row = $db->table('ufs')->where('id_uf
        ', $siglaUf)->get()->getRow();
        return $row ? $row->id_uf : null;
    }

    /**
     * Exibe a tela de recuperação de senha
     */
    public function recuperarSenha()
    {
        $data = [];

        // Verifica se há mensagem de erro
        if (session()->has('error')) {
            $data['error'] = session()->getFlashdata('error');
        }

        // Verifica se há mensagem de sucesso
        if (session()->has('success')) {
            $data['success'] = session()->getFlashdata('success');
        }

        return $this->renderView('recuperar_senha', $data);
    }

    /**
     * Processa a solicitação de recuperação de senha
     */
    public function enviarRecuperacao()
    {
        $clienteModel = new ClienteModel();
        $email = $this->request->getPost('email');

        if (empty($email)) {
            return redirect()->to('/recuperar-senha')
                ->with('error', 'Por favor, informe seu e-mail.');
        }

        // Verifica se o e-mail existe
        if (!$clienteModel->emailExiste($email)) {
            return redirect()->to('/recuperar-senha')
                ->with('error', 'E-mail não cadastrado em nosso sistema.');
        }

        // Gera um token de recuperação
        $token = bin2hex(random_bytes(16));
        //$expira = time() + (24 * 60 * 60); // 24 horas

        // Salva o token no banco de dados
        $resultado = $clienteModel->salvarTokenRecuperacao($email, $token);

        if ($resultado) {
            // Envia o e-mail de recuperação (simulado)
            $linkRecuperacao = base_url("recuperar-senha/$token");

            // Em produção, aqui enviaria um e-mail real
            log_message('info', "E-mail de recuperação para {$email}: {$linkRecuperacao}");

            return redirect()->to('/recuperar-senha')
                ->with('success', 'Um link de recuperação foi enviado para seu e-mail.');
        } else {
            return redirect()->to('/recuperar-senha')
                ->with('error', 'Ocorreu um erro ao processar sua solicitação. Tente novamente.');
        }
    }

    /**
     * Retorna as cidades de um estado via AJAX
     */
    public function getCidades($uf = null)
    {
        if (!$uf) {
            return $this->response->setJSON(['error' => 'UF não informada']);
        }

        $cidades = $this->municipioModel->getByUf($uf);
        return $this->response->setJSON(['cidades' => $cidades]);
    }

    /**
     * Exibe a página da conta do cliente
     */
    public function minhaConta()
    {
        // Verifica se o cliente está logado
        if (!session()->has('cliente')) {
            session()->set('redirect_after_login', '/minha-conta');
            return redirect()->to('/login')
                ->with('error', 'Você precisa fazer login para acessar esta página.');
        }

        $idCliente = session()->get('cliente')['id'];
        $idEmpresa = session()->get('cliente')['id_Empresa'];

        // Carrega os dados do cliente
        $cliente = $this->clienteModel->getById($idCliente, $idEmpresa);

        if (!$cliente) {
            session()->remove('cliente');
            return redirect()->to('/login')
                ->with('error', 'Sua sessão expirou. Faça login novamente.');
        }

        // Carrega os estados e cidades
        $estados = $this->ufModel->getAll();
        $cidades = [];

        if (!empty($cliente['id_uf'])) {
            $cidades = $this->municipioModel->getByUf($cliente['id_uf']);
        }

        $data = [
            'cliente' => $cliente,
            'estados' => $estados,
            'cidades' => $cidades
        ];

        // Verifica se há mensagens flash
        if (session()->has('success')) {
            $data['success'] = session()->getFlashdata('success');
        }

        if (session()->has('error')) {
            $data['error'] = session()->getFlashdata('error');
        }

        return $this->renderView('minha_conta', $data);
    }

    public function alterarSenha()
    {

        $clienteSessao = session()->get('cliente');
        if (!$clienteSessao) {
            return redirect()->to('/login')->with('error', 'Você precisa estar logado');
        }

        $idCliente = is_array($clienteSessao) ? ($clienteSessao['id'] ?? null) : ($clienteSessao->id ?? null);

        // Busca os dados do cliente no banco
        $cliente = (array) $this->clienteModel->find($idCliente);

        return $this->renderView('alterarSenha', $cliente); // sua view aqui
    }

    public function processarAlteracaoSenha()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'senha_atual' => 'required',
                'nova_senha' => 'required|min_length[6]',
                'confirmar_senha' => 'required|matches[nova_senha]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idCliente = session()->get('cliente')['id'];
            $cliente = $this->clienteModel->find($idCliente);


            if (!$cliente || !password_verify($this->request->getPost('senha_atual'), $cliente['senha'])) {
                return redirect()->back()->with('error', 'A senha atual está incorreta.');
            }

            // Verificar se nova senha é igual à atual
            if (password_verify($this->request->getPost('nova_senha'), $cliente['senha'])) {
                return redirect()->back()->with('error', 'A nova senha não pode ser igual à senha atual.');
            }

            // Atualiza com a nova senha
            $novaSenha = password_hash($this->request->getPost('senha_novo'), PASSWORD_DEFAULT);
            $this->clienteModel->update($idCliente, ['senha' => $novaSenha]);

            return redirect()->to('/minha-conta')->with('success', 'Senha alterada com sucesso.');
        }
    }

}