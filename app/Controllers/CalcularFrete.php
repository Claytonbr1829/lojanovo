<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProdutoModel;
use CodeIgniter\API\ResponseTrait;
use DateTime;

class CalcularFrete extends BaseController
{
    use ResponseTrait;


    public function index()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $cepDestino = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));

        // Validação do CEP
        if (strlen($cepDestino) !== 8) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'CEP inválido'
            ]);
        }

        try {

            $produtosParaFrete = $this->getProdutosDaSessao();

            if (empty($produtosParaFrete)) {
                return $this->fail('Nenhum produto no carrinho', 422);
            }

            $fretes = $this->consultarSuperFrete($cepDestino, $produtosParaFrete);
            if (isset($fretes['error'])) {
                return $this->fail($fretes['error'], 400);
            }

            return $this->respond([
                'success' => true,
                'data' => [
                    'html' => $this->montarHtmlFretes($fretes),
                    'opcoes' => $fretes
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro no cálculo de frete: ' . $e->getMessage());
            return $this->failServerError('Erro interno no servidor: ' . $e->getMessage());
        }
    }

    protected function getProdutosDaSessao(): array
    {
        if (!session()->has('carrinho') || empty(session()->get('carrinho'))) {
            return [];
        }

        $carrinho = session()->get('carrinho');
        $produtoModel = new ProdutoModel();
        $produtosParaFrete = [];

        foreach ($carrinho as $idProduto => $quantidade) {
            $produto = $produtoModel->find($idProduto);

            if ($produto) {
                $produtosParaFrete[] = [
                    'id_produto' => $produto['id_produto'],
                    'peso' => (float) ($produto['peso'] ?? 0.3),
                    'valor' => (float) ($produto['preco'] ?? 0),
                    'largura' => (float) ($produto['largura'] ?? 11),
                    'altura' => (float) ($produto['altura'] ?? 2),
                    'comprimento' => (float) ($produto['comprimento'] ?? 16),
                    'quantidade' => (int) $quantidade
                ];
            }
        }

        return $produtosParaFrete;
    }

    protected function consultarSuperFrete(string $cepDestino, $produtosParaFrete): array
    {
        // Calcula as dimensões totais do pacote (simplificado)
        //$package = $this->calcularDimensoesPacote($produtosParaFrete);


        $superFreteConfig = [
            'sandbox' => true, // Mude para false em produção
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NDgyMjI1NjksInN1YiI6IjJTcVNZMlFBOHFlTmNyb2xRVzJ1cFBHczY5NzMifQ.oZu6CymzIZbd686H3RpU6C_bIHyvfPAqzot-hXGK-pA',
            'cep_origem' => '31150340', // Seu CEP de origem
            'timeout' => 30,
            'services' => '1,2,3,4' // Códigos dos serviços (1=PAC, 2=SEDEX, etc)
        ];

        $package = $this->calcularDimensoesPacote($produtosParaFrete);

        $payload = [
            'from' => [
                'postal_code' => $superFreteConfig['cep_origem']
            ],
            'to' => [
                'postal_code' => $cepDestino
            ],
            'services' => $superFreteConfig['services'],
            'package' => $package,

        ];


        $client = \Config\Services::curlrequest();
        try {
            $baseUrl = $superFreteConfig['sandbox']
                ? 'https://sandbox.superfrete.com'
                : 'https://web.superfrete.com';

            // Adicionei verificação de DNS antes da requisição
            if (!gethostbyname(parse_url($baseUrl, PHP_URL_HOST))) {
                throw new \Exception("Não foi possível resolver o host do SuperFrete");
            }

            $response = $client->post("{$baseUrl}/api/v0/calculator", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $superFreteConfig['token'],
                    'User-Agent' => 'Superfrete claytonbramos@hotmail.com'
                ],
                'json' => $payload,
                'timeout' => $superFreteConfig['timeout']
            ]);


            $body = json_decode($response->getBody(), true);
            return $this->processarRespostaSuperFrete($body);

        } catch (\Exception $e) {
            log_message('error', 'Erro SuperFrete: ' . $e->getMessage());
            return ['error' => 'Erro ao conectar com SuperFrete: ' . $e->getMessage()];
        }
    }

    protected function calcularDimensoesPacote(array $produtos): array
    {
        // Inicializa variáveis
        $maiorAltura = 0;
        $maiorLargura = 0;
        $maiorComprimento = 0;
        $pesoTotal = 0;

        foreach ($produtos as $produto) {
            // Multiplica as dimensões pela quantidade (se necessário)
            $quantidade = $produto['quantidade'] ?? 1;

            // Encontra a maior dimensão de cada eixo
            $maiorAltura = max($maiorAltura, $produto['altura'] * $quantidade);
            $maiorLargura = max($maiorLargura, $produto['largura'] * $quantidade);
            $maiorComprimento = max($maiorComprimento, $produto['comprimento'] * $quantidade);

            // Soma os pesos
            $pesoTotal += $produto['peso'] * $quantidade;
        }

        // Aplica dimensões mínimas e margens de segurança
        return [
            'height' => max(2, $maiorAltura + 1),  // Mínimo 2cm + margem
            'width' => max(11, $maiorLargura + 1), // Mínimo 11cm + margem
            'length' => max(16, $maiorComprimento + 1), // Mínimo 16cm + margem
            'weight' => max(0.3, $pesoTotal) // Mínimo 0.3kg
        ];
    }

    protected function processarRespostaSuperFrete(array $response): array
    {
        if (empty($response)) {
            return ['error' => 'Resposta vazia do SuperFrete'];
        }

        if (isset($response['errors'])) {
            return ['error' => implode(', ', $response['errors'])];
        }

        $resultado = [];
        foreach ($response as $servico) {
            if (!isset($servico['id'], $servico['name'], $servico['price'], $servico['delivery_time'])) {
                continue;
            }

            $resultado[] = [
                'id' => $servico['id'],
                'servico' => $servico['name'],
                'valor' => (float) $servico['price'],
                'prazo' => (int) $servico['delivery_time'],
                'empresa' => $servico['company']['name'] ?? 'Desconhecido'
            ];
        }

        return empty($resultado) ? ['error' => 'Nenhuma opção de frete disponível'] : $resultado;
    }

    private function diaSemanaPtBr($data)
    {
        // Converte a data do formato d/m/Y para um formato que strtotime entenda
        $dataConvertida = DateTime::createFromFormat('d/m/Y', $data);
        if (!$dataConvertida) {
            return $data; // Retorna a data original se não puder converter
        }

        $dias = [
            'Sunday' => 'Domingo',
            'Monday' => 'Segunda-feira',
            'Tuesday' => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday' => 'Quinta-feira',
            'Friday' => 'Sexta-feira',
            'Saturday' => 'Sábado'
        ];

        $diaSemanaIngles = $dataConvertida->format('l');
        return $dias[$diaSemanaIngles] ?? $diaSemanaIngles;
    }

    protected function montarHtmlFretes(array $fretes): string
    {
        if (empty($fretes)) {
            return '<div class="alert alert-warning">Nenhuma opção de frete disponível</div>';
        }

        // Cabeçalho da tabela
        $html = '<table class="table table-striped table-advance table-hover">' .
            '<thead>' .
            '<tr>' .
            '<th>' .
            '<span><i class="fas fa-truck"></i></span>&nbsp;' .
            '<label class="lbImg">Tipo de Entrega</label><br/>' .
            '</th>' .
            '<th>' .
            '<span><i class="fas fa-clock"></i></span>&nbsp;' .
            '<label class="lbImg">Produção e Postagem</label><br/>' .
            '</th>' .
            '<th>' .
            '<span><i class="fas fa-calendar-alt"></i></span>&nbsp;' .
            '<label class="lbImg">Previsão de Entrega</label><br/>' .
            '</th>' .
            '<th>' .
            '<span><i class="fas fa-money-bill-wave"></i></span>&nbsp;' .
            '<label class="lbImg">Valor</label><br/>' .
            '</th>' .
            '</tr>' .
            '</thead>' .
            '<tbody>';

        // Data de produção (hoje) e data de entrega (3 dias depois) no formato d/m/Y
        $dataProducao = date('d/m/Y');
        $dataEntrega = date('d/m/Y', strtotime('+3 days'));

        // Obter os dias da semana em português para produção e entrega
        $diaProducao = $this->diaSemanaPtBr($dataProducao);
        $diaEntrega = $this->diaSemanaPtBr($dataEntrega);

        foreach ($fretes as $index => $frete) {
            $html .= '<tr class="trEntregaGeral">' .
                '<td>' .
                '<input type="radio" class="opcao-frete" id="frete-' . $index . '" name="optionsRadiosEntregas" ' .
                'value="' . $index . '|' . htmlspecialchars($frete['servico'] ?? '') . '|' . $frete['valor'] . '" ' .
                'data-servico="' . htmlspecialchars($frete['servico'] ?? '') . '" ' .
                'data-valor="' . $frete['valor'] . '" data-prazo="' . $frete['prazo'] . '" >' .
                '<span for="frete-' . $index . '">' .
                '<i class="fas fa-shipping-fast"></i></span>&nbsp;' .
                '<label for="frete-' . $index . '" class="lbImg" style="margin-left:11px">' .
                htmlspecialchars($frete['empresa'] ?? '') . ' - ' .
                htmlspecialchars($frete['servico'] ?? '') . '</label>' .
                '</td>' .
                '<td>' .
                '<span for="frete-' . $index . '">' .
                '<i class="fas fa-edit text-muted"></i></span>' .
                '<label for="frete-' . $index . '" class="lbImg">&nbsp;' . $diaProducao . ' - ' . $dataProducao . '</label><br/>' .
                '</td>' .
                '<td>' .
                '<span for="frete-' . $index . '">' .
                '<i class="fas fa-exclamation-circle text-muted"></i></span>' .
                '<label for="frete-' . $index . '" class="lbImg">&nbsp;' . $diaEntrega . ' - ' . $dataEntrega . '</label>' .
                '</td>' .
                '<td>R$ ' . number_format($frete['valor'], 2, ',', '.') . '</td>' .
                '</tr>';
        }

        // Opção de retirada
        $html .= '<tr id="trRetirada" class="trEntregaGeral">' .
            '<td>' .
            '<input type="radio" id="optEntrega1" name="optionsRadiosEntregas" value="1|Retirada|0" >' .
            '<span for="optEntrega1">' .
            '<i class="fas fa-store"></i></span>&nbsp;' .
            '<label for="optEntrega1" class="lbImg" style="margin-left:11px">RETIRAR EM NOSSA LOJA</label>' .
            '</td>' .
            '<td>' .
            '<span for="optEntrega1">' .
            '<i class="fas fa-edit text-muted"></i></span>' .
            '<label for="optEntrega1" class="lbImg">&nbsp;' . $diaProducao . ' - ' . $dataProducao . '</label><br/>' .
            '</td>' .
            '<td>' .
            '<span for="optEntrega1">' .
            '<i class="fas fa-exclamation-circle text-muted"></i></span>' .
            '<label for="optEntrega1" class="lbImg">&nbsp;' . $diaProducao . ' - ' . $dataProducao . '</label>' .
            '</td>' .
            '<td>R$ 0,00</td>' .
            '</tr>';

        $html .= '</tbody></table>';

        return $html;
    }
}