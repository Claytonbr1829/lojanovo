<?php
namespace App\Models;

use PDO;
use Exception;

class PedidoModel extends PedidoModelBase
{
    private $modelCriacao;
    private $modelConsulta;

    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->modelCriacao = new PedidoModelCriacao();
        $this->modelConsulta = new PedidoModelConsulta();
    }

    /**
     * Cria um novo pedido
     */
    public function criar(array $pedido): int
    {
        return $this->modelCriacao->criar($pedido);
    }

    /**
     * Adiciona um item ao pedido
     */
    public function adicionarItem(int $idPedido, array $item): bool
    {
        return $this->modelCriacao->adicionarItem($idPedido, $item);
    }

    /**
     * Atualiza o status de um pedido
     */
    public function atualizarStatus(int $idPedido, int $status): bool
    {
        return $this->modelCriacao->atualizarStatus($idPedido, $status);
    }

    /**
     * Busca um pedido pelo ID
     */
    public function getById(int $id): ?array
    {
        return $this->modelConsulta->getById($id);
    }

    /**
     * Busca os itens de um pedido
     */
    public function getItensByPedido(int $idPedido): array
    {
        return $this->modelConsulta->getItensByPedido($idPedido);
    }

    /**
     * Busca os pedidos de um cliente
     */
    public function getByCliente(int $idCliente): array
    {
        return $this->modelConsulta->getByCliente($idCliente);
    }

    /**
     * Busca os pedidos recentes da loja
     */
    public function getRecentes(int $limit = 10): array
    {
        return $this->modelConsulta->getRecentes($limit);
    }

    /**
     * Conta o total de pedidos por status
     */
    public function contarPorStatus(): array
    {
        return $this->modelConsulta->contarPorStatus();
    }

    /**
     * Busca um pedido pelo token
     */
    // public function findPedido($token): ?array
    // {
    //     return $this->select('pedidos.*, clientes.nome, clientes.email, clientes.cpf')
    //         ->join('clientes', 'clientes.id_cliente = pedidos.id_cliente')
    //         ->where('pedidos.token', $token)
    //         ->first();
    // }

    public function findPedido($token): array
    {
        $pedido = (new PedidoModelBase());

        $resultado = $pedido
        ->join('clientes', 'clientes.id_cliente = pedidos.id_cliente')
        ->join('pedidos_itens', 'pedidos_itens.id_pedido = pedidos.id_pedido')
        ->join('pedidos_pagamento', 'pedidos_pagamento.id_pedido = pedidos.id_pedido')
        ->where('token', $token)->first();

        // var_dump($resultado);exit;

        return $resultado;
    }

}