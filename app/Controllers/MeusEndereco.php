<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClienteModel;
use App\Models\PedidoModel;
use App\Models\MunicipioModel;
use App\Models\UfModel;

class MeusEndereco extends BaseController
{
    protected $municipioModel;
    protected $ufModel;

    public function __construct()
    {
        $this->municipioModel = new MunicipioModel();
        $this->ufModel = new UfModel();
    }

    public function index()
    {
        if (!session()->has('cliente')) {
            return redirect()->to('/login')->with('error', 'Você precisa estar logado.');
        }

        $idCliente = session()->get('cliente')['id'];
        $clienteModel = new ClienteModel();
        $pedidoModel = new PedidoModel();

        $clienteCompleto = $clienteModel->find($idCliente);

        // $municipioNome = '---';
        // $estadoNome = '---';


        if (!empty($clienteCompleto) && !is_null($clienteCompleto)) {
            // Busca município pelo nome
            if (!empty($clienteCompleto->id_municipio)) {
                $municipio = $this->municipioModel
                    ->where('id_municipio', $clienteCompleto->id_municipio)
                    ->first();

                if (!empty($municipio)) {
                    $municipioNome = $municipio['municipio'];

                    // Busca UF pela relação do município
                    if (!empty($municipio['id_uf'])) {
                        $uf = $this->ufModel->find($municipio['id_uf']);
                        $estadoNome = $uf['sigla'] ?? $uf['estado'] ?? '---';
                    }
                }
            }
        }

        // Endereço de faturamento (cadastro do cliente)
        $enderecos['faturamento'] = [
            'logradouro' => $clienteCompleto->logradouro,
            'numero' => $clienteCompleto->numero,
            'complemento' => $clienteCompleto->complemento,
            'bairro' => $clienteCompleto->bairro,
            'municipio' => $municipioNome,
            'estado' => $estadoNome,
            'cep' => $clienteCompleto->cep,
            'fonte' => 'Cadastro do Cliente'
        ];

        // Busca o último pedido
        $pedido = $pedidoModel
            ->where('id_cliente', $idCliente)
            ->orderBy('id_pedido', 'DESC')
            ->first();

        if ($pedido && $pedido['endereco_diferente']) {
            $enderecos['entrega'] = [
                'logradouro' => $pedido['endereco_rua'],
                'numero' => $pedido['numero_rua'],
                'complemento' => $pedido['complemento'],
                'bairro' => $pedido['bairro'],
                'municipio' => $pedido['municipio'],
                'estado' => $pedido['estado'],
                'cep' => $pedido['cep'],
                'fonte' => 'Pedido #' . $pedido['id_pedido']
            ];
        } else {
            $enderecos['entrega'] = $enderecos['faturamento'];
        }

        return $this->renderView('meusendereco', [
            'enderecos' => $enderecos
        ]);
    }

    public function salvar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'erro', 'mensagem' => 'Requisição inválida.']);
        }

        $tipo = $this->request->getPost('tipo');
        $data = [
            'logradouro' => $this->request->getPost('logradouro'),
            'numero' => $this->request->getPost('numero'),
            'complemento' => $this->request->getPost('complemento'),
            'bairro' => $this->request->getPost('bairro'),
            'municipio' => $this->request->getPost('municipio'),
            'estado' => $this->request->getPost('estado'),
            'cep' => $this->request->getPost('cep'),
        ];

        // Aqui você pode salvar em banco, associar ao cliente, etc.
        // Exemplo: atualiza o cadastro do cliente
        $idCliente = session()->get('cliente')['id'];
        $clienteModel = new ClienteModel();

        if ($tipo === 'faturamento') {
            $clienteModel->update($idCliente, $data);
        }

        return $this->response->setJSON(['status' => 'ok']);
    }


}
