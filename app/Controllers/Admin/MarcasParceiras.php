<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MarcaParceiraModel;
use App\Models\ConfiguracaoModel;

class MarcasParceiras extends BaseController
{
    protected $marcaParceiraModel;
    protected $configuracaoModel;

    public function __construct()
    {
        $this->marcaParceiraModel = new MarcaParceiraModel();
        $this->configuracaoModel = new ConfiguracaoModel();
    }

    /**
     * Exibe a lista de marcas parceiras
     */
    public function index()
    {
        try {
            // Verificar autenticação
            if (!session()->get('admin_logado')) {
                return redirect()->to('/admin/login')
                    ->with('error', 'Você precisa fazer login para acessar esta página.');
            }

            // Busca as configurações da loja
            $config = $this->configuracaoModel->getConfiguracoes();
            
            // Busca todas as marcas parceiras
            $marcasParceiras = $this->marcaParceiraModel->getMarcasParceiras(100);
            
            // Prepara os dados para a view
            $data = [
                'marcasParceiras' => $marcasParceiras,
                'config' => $config,
                'titulo' => 'Gerenciar Marcas Parceiras'
            ];
            
            // Renderiza a view
            return view('Admin/marcas_parceiras', $data);
            
        } catch (\Exception $e) {
            // Log do erro
            log_message('error', $e->getMessage());
            
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Erro ao carregar marcas parceiras: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para adicionar uma nova marca parceira
     */
    public function create()
    {
        try {
            // Verificar autenticação
            if (!session()->get('admin_logado')) {
                return redirect()->to('/admin/login')
                    ->with('error', 'Você precisa fazer login para acessar esta página.');
            }

            // Busca as configurações da loja
            $config = $this->configuracaoModel->getConfiguracoes();
            
            // Prepara os dados para a view
            $data = [
                'config' => $config,
                'titulo' => 'Adicionar Nova Marca Parceira',
                'marca' => [
                    'id' => '',
                    'nome' => '',
                    'logo' => '',
                    'link' => '',
                    'ativo' => 1,
                    'ordem' => 0
                ],
                'acao' => 'adicionar'
            ];
            
            // Renderiza a view
            return view('Admin/marca_parceira_form', $data);
            
        } catch (\Exception $e) {
            // Log do erro
            log_message('error', $e->getMessage());
            
            return redirect()->to('/admin/marcas-parceiras')
                ->with('error', 'Erro ao carregar formulário: ' . $e->getMessage());
        }
    }

    /**
     * Processa o formulário para salvar uma nova marca parceira
     */
    public function store()
    {
        try {
            // Verificar autenticação
            if (!session()->get('admin_logado')) {
                return redirect()->to('/admin/login')
                    ->with('error', 'Você precisa fazer login para acessar esta página.');
            }

            // Obter dados do formulário
            $dados = $this->request->getPost();
            
            // Processar upload de imagem
            $logo = $this->request->getFile('logo');
            
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $filename = $this->moveUploadedFile($logo);
                $dados['logo'] = $filename;
            } else {
                $dados['logo'] = 'marca-default.png';
            }
            
            // Inserir no banco de dados
            $resultado = $this->marcaParceiraModel->inserirMarcaParceira($dados);
            
            if ($resultado) {
                return redirect()->to('/admin/marcas-parceiras')
                    ->with('success', 'Marca parceira adicionada com sucesso!');
            } else {
                return redirect()->to('/admin/marcas-parceiras')
                    ->with('error', 'Erro ao adicionar marca parceira.');
            }
            
        } catch (\Exception $e) {
            // Log do erro
            log_message('error', $e->getMessage());
            
            return redirect()->to('/admin/marcas-parceiras/adicionar')
                ->with('error', 'Erro ao salvar marca parceira: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar uma marca parceira
     */
    public function edit($id = null)
    {
        try {
            // Verificar autenticação
            if (!session()->get('admin_logado')) {
                return redirect()->to('/admin/login')
                    ->with('error', 'Você precisa fazer login para acessar esta página.');
            }

            if (empty($id)) {
                return redirect()->to('/admin/marcas-parceiras')
                    ->with('error', 'ID da marca parceira não fornecido.');
            }
            
            // Busca a marca parceira
            $marca = $this->marcaParceiraModel->getMarcaParceira($id);
            
            if (!$marca) {
                return redirect()->to('/admin/marcas-parceiras')
                    ->with('error', 'Marca parceira não encontrada.');
            }
            
            // Busca as configurações da loja
            $config = $this->configuracaoModel->getConfiguracoes();
            
            // Prepara os dados para a view
            $data = [
                'config' => $config,
                'titulo' => 'Editar Marca Parceira',
                'marca' => $marca,
                'acao' => 'editar'
            ];
            
            // Renderiza a view
            return view('Admin/marca_parceira_form', $data);
            
        } catch (\Exception $e) {
            // Log do erro
            log_message('error', $e->getMessage());
            
            return redirect()->to('/admin/marcas-parceiras')
                ->with('error', 'Erro ao carregar formulário: ' . $e->getMessage());
        }
    }

    /**
     * Processa o formulário para atualizar uma marca parceira
     */
    public function update($id = null)
    {
        try {
            // Verificar autenticação
            if (!session()->get('admin_logado')) {
                return redirect()->to('/admin/login')
                    ->with('error', 'Você precisa fazer login para acessar esta página.');
            }

            if (empty($id)) {
                return redirect()->to('/admin/marcas-parceiras')
                    ->with('error', 'ID da marca parceira não fornecido.');
            }
            
            // Obter dados do formulário
            $dados = $this->request->getPost();
            
            // Processar upload de imagem
            $logo = $this->request->getFile('logo');
            
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $filename = $this->moveUploadedFile($logo);
                $dados['logo'] = $filename;
            }
            
            // Atualizar no banco de dados
            $resultado = $this->marcaParceiraModel->atualizarMarcaParceira($id, $dados);
            
            if ($resultado) {
                return redirect()->to('/admin/marcas-parceiras')
                    ->with('success', 'Marca parceira atualizada com sucesso!');
            } else {
                return redirect()->to('/admin/marcas-parceiras')
                    ->with('error', 'Erro ao atualizar marca parceira.');
            }
            
        } catch (\Exception $e) {
            // Log do erro
            log_message('error', $e->getMessage());
            
            return redirect()->to('/admin/marcas-parceiras/editar/' . $id)
                ->with('error', 'Erro ao atualizar marca parceira: ' . $e->getMessage());
        }
    }

    /**
     * Remove uma marca parceira
     */
    public function delete($id = null)
    {
        try {
            // Verificar autenticação
            if (!session()->get('admin_logado')) {
                return redirect()->to('/admin/login')
                    ->with('error', 'Você precisa fazer login para acessar esta página.');
            }

            if (empty($id)) {
                return redirect()->to('/admin/marcas-parceiras')
                    ->with('error', 'ID da marca parceira não fornecido.');
            }
            
            // Remover do banco de dados
            $resultado = $this->marcaParceiraModel->excluirMarcaParceira($id);
            
            if ($resultado) {
                return redirect()->to('/admin/marcas-parceiras')
                    ->with('success', 'Marca parceira removida com sucesso!');
            } else {
                return redirect()->to('/admin/marcas-parceiras')
                    ->with('error', 'Erro ao remover marca parceira.');
            }
            
        } catch (\Exception $e) {
            // Log do erro
            log_message('error', $e->getMessage());
            
            return redirect()->to('/admin/marcas-parceiras')
                ->with('error', 'Erro ao remover marca parceira: ' . $e->getMessage());
        }
    }

    /**
     * Move o arquivo enviado para o diretório de uploads
     */
    private function moveUploadedFile($uploadedFile): string
    {
        $newName = $uploadedFile->getRandomName();
        $directory = ROOTPATH . 'public/uploads/marcas';
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $uploadedFile->move($directory, $newName);
        
        return $newName;
    }
} 