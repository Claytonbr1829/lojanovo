<?php

namespace App\Controllers;

use App\Models\ClienteModel;

class Conta extends BaseController
{
    public function minhaConta()
    {
        $session = session();
        $cliente = $session->get('cliente');

        if (!$cliente || !$cliente['logged_in']) {
            return redirect()->to(site_url('login'));
        }

        $model = new ClienteModel();
        $clienteData = $model->find($cliente['id']);
        
        // Converter objeto para array
        $clienteData = json_decode(json_encode($clienteData), true);

        if ($this->request->getMethod() === 'post') {
            // Validação dos dados (exemplo simples, adapte para seu caso)
            $rules = [
                'tipo' => 'required|in_list[1,2]',
                'nome' => 'required|min_length[3]',
                'numero_documento' => 'required',
                'numero_identidade' => 'required',
                'cep' => 'required',
                'logradouro' => 'required',
                'numero' => 'required',
                'bairro' => 'required',
                'cidade' => 'required',
                'uf' => 'required',
                'text_cel' => 'required',
                'text_fixo' => 'permit_empty',
                'email' => 'required|valid_email',
            ];

            if (!$this->validate($rules)) {
                return view('minha_conta', [
                    'errors' => $this->validator->getErrors(),
                    'old' => $this->request->getPost(),
                    'cliente' => $clienteData
                ]);
            }

            // Atualiza dados do cliente
            $dadosAtualizados = [
                'tipo' => $this->request->getPost('tipo'),
                'nome' => $this->request->getPost('nome'),
                'numero_documento' => $this->request->getPost('numero_documento'),
                'numero_identidade' => $this->request->getPost('numero_identidade'),
                'cep' => $this->request->getPost('cep'),
                'logradouro' => $this->request->getPost('logradouro'),
                'numero' => $this->request->getPost('numero'),
                'bairro' => $this->request->getPost('bairro'),
                'cidade' => $this->request->getPost('cidade'),
                'uf' => $this->request->getPost('uf'),
                'text_cel' => $this->request->getPost('text_cel'),
                'text_fixo' => $this->request->getPost('text_fixo'),
                'email' => $this->request->getPost('email'),
            ];

            // Se for alterar senha, você pode implementar aqui (com validação)
            $senha = $this->request->getPost('senha');
            if (!empty($senha)) {
                // Coloque validação e hash da senha
                $dadosAtualizados['senha'] = password_hash($senha, PASSWORD_DEFAULT);
            }

            $model->update($cliente['id'], $dadosAtualizados);
            

            // Atualizar session com dados novos
            $clienteAtualizado = $model->find($cliente['id']);
            $clienteAtualizado = json_decode(json_encode($clienteAtualizado),true);
            $clienteAtualizado['logged_in'] = true;
            $session->set('cliente', $clienteAtualizado);

            return $this->renderview('minha-conta', [
                'success' => 'Dados atualizados com sucesso!',
                'old' => $clienteAtualizado,
                'cliente' => $clienteAtualizado
            ]);
        }

        // Se for GET, exibir os dados do cliente para preencher o formulário
        return $this->renderview('minhaconta', [
            'old' => $clienteData,
            'cliente' => $clienteData
        ]);
    }
}
