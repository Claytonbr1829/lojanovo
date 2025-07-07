<?php

namespace App\Controllers;

use App\Models\PedidoModel;
use App\Models\PedidoItemModel;
use App\Models\ProdutoModel;
use CodeIgniter\API\ResponseTrait;

class MeusPedidos extends BaseController
{
    use ResponseTrait;

    protected $pedidoModel;
    protected $pedidoItemModel;
    protected $produtoModel;

    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();
        $this->pedidoItemModel = new PedidoItemModel();
        $this->produtoModel = new ProdutoModel();
    }

    public function index()
    {
        // Verifica se o cliente está logado
        if (!session()->has('cliente')) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login para ver seus pedidos.');
        }

        $idCliente = session()->get('cliente')['id'];

        // Configuração padrão de paginação
        $perPage = $this->request->getGet('per_page') ?? 10;

        // Busca os pedidos com paginação
        $pedidos = $this->pedidoModel->where('id_cliente', $idCliente)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        $data = [
            'pedidos' => $this->formatarPedidos($pedidos),
            'pager' => $this->pedidoModel->pager,
            'per_page' => $perPage,
            'title' => 'Meus Pedidos'
        ];

        return $this->renderView('meusPedidos', $data);
    }

    public function pesquisar()
    {
        echo ('chegou');
        // Verifica se o cliente está logado
        if (!session()->has('cliente')) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login para ver seus pedidos.');
        }

        $idCliente = session()->get('cliente')['id'];
        var_dump($idCliente);
        $perPage = $this->request->getGet('per_page') ?? 10;
        $tipoPesquisa = $this->request->getPost('tipo_pesquisa');
        $dataInicio = $this->request->getPost('data_inicio');
        $dataFim = $this->request->getPost('data_fim');
        $numeroPedido = $this->request->getPost('numero_pedido');

        $builder = $this->pedidoModel->where('id_cliente', $idCliente);

        if ($tipoPesquisa == '1' && !empty($dataInicio) && !empty($dataFim)) {
            // Pesquisa por período
            $builder->where('DATE(created_at) >=', $dataInicio)
                ->where('DATE(created_at) <=', $dataFim);
        } elseif ($tipoPesquisa == '2' && !empty($numeroPedido)) {
            // Pesquisa por número do pedido (remove traço se existir)
            $numeroPedido = str_replace('-', '', $numeroPedido);
            $builder->like('id_pedido', $numeroPedido, 'after');
        }

        $pedidos = $builder->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        $data = [
            'pedidos' => $this->formatarPedidos($pedidos),
            'pager' => $this->pedidoModel->pager,
            'per_page' => $perPage,
            'title' => 'Resultado da Pesquisa'
        ];

        return $this->renderView('meuspedidos', $data);
    }

    public function detalhesPedido($idPedido)
    {
        // Verifica se o cliente está logado
        if (!session()->has('cliente')) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login para ver seus pedidos.');
        }

        $idCliente = session()->get('cliente')['id'];

        // Busca o pedido
        $pedido = $this->pedidoModel->where('id_pedido', $idPedido)
            ->where('id_cliente', $idCliente)
            ->first();

        if (!$pedido) {
            return redirect()->to('/meuspedidos')->with('error', 'Pedido não encontrado.');
        }

        // Busca os itens do pedido
        $itens = $this->pedidoItemModel
                        ->select('pedidos_itens.*, produtos.arquivo as arquivo')
                        ->where('pedidos_itens.id_pedido', $idPedido)
                        ->join('produtos', 'produtos.id_produto = pedidos_itens.id_produto', 'left')
                        ->findAll();

        // if (empty($itens['imagem'])) {
        //     $produto['imagem'] = 'produto-default.jpg';
        // }
        
        $produto = $this->produtoModel->findAll();
        $data = [
            'pedido' => $pedido,
            'itens' => $itens,
            'produto' => $produto,
            'title' => 'Detalhes do Pedido #' . $this->formatarNumeroPedido($idPedido)
        ];

        return $this->renderview('detalhespedido', $data);
    }

    public function downloadNota($idPedido)
    {
        // Verifica se o cliente está logado
        if (!session()->has('cliente')) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login para ver seus pedidos.');
        }

        $idCliente = session()->get('cliente')['id'];

        $pedido = $this->pedidoModel->where('id_pedido', $idPedido)
            ->where('id_cliente', $idCliente)
            ->first();

        if (!$pedido || empty($pedido['caminho_nota_fiscal'])) {
            return redirect()->back()->with('error', 'Nota fiscal não disponível para download.');
        }

        return $this->response->download($pedido['caminho_nota_fiscal'], null);
    }

    protected function formatarPedidos(array $pedidos): array
    {
        return array_map([$this, 'formatarPedido'], $pedidos);
    }

    protected function formatarPedido(array $pedido): array
    {
        $statusConfig = $this->getStatusConfig($pedido['status_pedido']);

        // Busca os itens do pedido para mostrar o primeiro produto
        $itens = $this->pedidoItemModel->where('id_pedido', $pedido['id_pedido'])->findAll();
        $primeiroItem = $itens[0] ?? null;

        return [
            'id_pedido' => $pedido['id_pedido'],
            'numero_formatado' => $this->formatarNumeroPedido($pedido['id_pedido']),
            'data_pedido' => $pedido['created_at'],
            'valor_total' => $pedido['preco_total'],
            'status' => $statusConfig['text'],
            'cor_status' => $statusConfig['color'],
            'permite_upload' => in_array($pedido['status_pedido'], ['Aguardando Arquivos', 'Processando']),
            'nota_fiscal' => $pedido['nf'] ?? null,
            'caminho_nota_fiscal' => $pedido['caminho_nota_fiscal'] ?? null,
            'nome_produto' => $primeiroItem['nome'] ?? 'Produto não especificado',
            'quantidade_itens' => count($itens)
        ];
    }

    protected function formatarNumeroPedido(int $idPedido): string
    {
        // Retorna apenas o número sem zeros à esquerda
        return (string) $idPedido;
    }

    protected function getStatusConfig(?string $status): array
    {
        $config = [
            'Pronto para retirada' => ['text' => 'Pronto para retirada', 'color' => '#eaf1de'],
            'Pagamento Pendente' => ['text' => 'Pagamento Pendente', 'color' => '#fae3e4'],
            'Pedido Excluído' => ['text' => 'Pedido Excluído', 'color' => '#eaf1de'],
            'Aguardando Execução' => ['text' => 'Aguardando Execução', 'color' => '#f9e2e4'],
            'Pedido Cancelado' => ['text' => 'Pedido Cancelado', 'color' => '#f6e2e4'],
            'Processando' => ['text' => 'Processando', 'color' => '#eaf1de'],
            'default' => ['text' => $status ?? 'Desconhecido', 'color' => '#ffffff']
        ];

        return $config[$status] ?? $config['default'];
    }
}