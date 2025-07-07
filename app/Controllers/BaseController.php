<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\ConfiguracaoModel;
use App\Models\AparenciaModel;
use App\Models\CategoriaModel;

abstract class BaseController extends Controller
{
	protected $request;
	protected $response;
	protected $logger;

	protected $viewData = [];
	protected $idEmpresa = null;

	protected $configuracaoModel;
	protected $aparenciaModel;
	protected $categoriaModel;

	protected $session;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->session = \Config\Services::session();

		$this->loadStoreConfig();
		$this->idEmpresa = $this->getEmpresaId();

		$this->configuracaoModel = new ConfiguracaoModel();
		$this->aparenciaModel = new AparenciaModel();
		$this->categoriaModel = new CategoriaModel();

		$this->loadCommonViewData();
	}

	protected function loadStoreConfig()
	{
		date_default_timezone_set('America/Sao_Paulo');

		if (!$this->session->has('carrinho')) {
			$this->session->set('carrinho', []);
		}

		$carrinho = $this->session->get('carrinho') ?? [];
		$totalItens = 0;
		$totalValor = 0;

		foreach ($carrinho as $item) {
			$totalItens += $item['quantidade'] ?? 0;
			$totalValor += ($item['preco'] ?? 0) * ($item['quantidade'] ?? 0);
		}

		$this->session->set('carrinho_quantidade', $totalItens);
		$this->session->set('carrinho_total', $totalValor);
	}

	protected function getEmpresaId(): ?int
	{
		$session = session();
		$forceRefresh = $this->request->getGet('refresh_config') !== null;

		if (!$forceRefresh && $session->has('empresa_id')) {
			return $session->get('empresa_id');
		}

		try {
			$currentHost = filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_URL);
			$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
			$fullUrl = $scheme . $currentHost;

			$cacheKey = 'empresa_id_' . md5($currentHost);
			$cachedEmpresaId = cache($cacheKey);
			if ($cachedEmpresaId) {
				$session->set('empresa_id', $cachedEmpresaId);
				return $cachedEmpresaId;
			}

			$db = \Config\Database::connect();
			$builder = $db->table('loja_config');
			$builder->select('id_empresa');

			$query = $builder->groupStart()
				->like('url', $currentHost)
				->orLike('url', $fullUrl)
				->groupEnd()
				->get();

			if ($query->getNumRows() > 0) {
				$result = $query->getRow();
				$empresaId = (int) $result->id_empresa;

				$session->set('empresa_id', $empresaId);
				cache()->save($cacheKey, $empresaId, 3600);

				return $empresaId;
			}

			$session->remove('empresa_id');
			return null;
		} catch (\Exception $e) {
			log_message('error', 'Erro ao buscar ID da empresa: ' . $e->getMessage());
			return null;
		}
	}

	protected function loadCommonViewData()
	{
		try {
			$config = $this->configuracaoModel->getConfiguracoes($this->idEmpresa);
			$aparencia = $this->aparenciaModel->getConfiguracoes($this->idEmpresa);
			$logo = $this->configuracaoModel->getLogo();
			$modoCss = $this->aparenciaModel->getCssDinamico($this->idEmpresa);
			$categorias = $this->categoriaModel->getCategoriasMenu(10, $this->idEmpresa);

			$carrinho = [
				'quantidade' => session()->get('carrinho_quantidade') ?? 0,
				'total' => session()->get('carrinho_total') ?? 0.00,
			];

			$this->viewData = [
				'config' => $config,
				'aparencia' => $aparencia,
				'logo' => $logo,
				'modoCss' => $modoCss,
				'categorias' => $categorias,
				'carrinho' => $carrinho,
				'idEmpresa' => $this->idEmpresa,
			];
		} catch (\Exception $e) {
			log_message('error', 'Erro ao carregar dados comuns: ' . $e->getMessage());

			$this->viewData = [
				'config' => [
					'nome_loja' => 'SwapShop',
					'meta_titulo' => 'SwapShop - Sua loja virtual',
					'meta_descricao' => 'Sua loja virtual completa',
					'mostrar_precos' => 1,
					'mostrar_depoimentos' => 1,
					'mostrar_contato_rodape' => 1,
					'mostrar_mais_vendidos' => 1,
					'mostrar_marcas_parceiras' => 1,
					'mostrar_assine_Newletter' => 1,
				],
				'aparencia' => [
					'cor_primaria' => '#007bff',
					'cor_secundaria' => '#6c757d',
					'cor_texto' => '#212529',
					'cor_fundo' => '#ffffff',
					'fonte' => 'Roboto, sans-serif',
				],
				'logo' => 'logo-default.png',
				'modoCss' => '',
				'categorias' => [],
				'carrinho' => [
					'quantidade' => 0,
					'total' => 0.00,
				],
				'idEmpresa' => $this->idEmpresa,
			];
		}
	}

	protected function renderView(string $view, array $data = [], bool $return = false)
	{
		try {
			$viewData = array_merge($this->viewData, $data);
			$content = view($view, $viewData);
			$viewData['content'] = $content;

			return view('templates/layout', $viewData, ['debug' => false]);
		} catch (\Exception $e) {
			log_message('error', 'Erro ao renderizar view: ' . $e->getMessage());

			if (ENVIRONMENT === 'production') {
				return view('errors/html/error_exception', [
					'title' => 'Erro ao carregar página',
					'message' => 'Desculpe, ocorreu um erro ao carregar a página. Por favor, tente novamente.'
				]);
			}
			throw $e;
		}
	}

	/**
	 * Verifica se a empresa foi identificada; se não, retorna redirecionamento.
	 */
	protected function verificaEmpresaOuRedireciona()
	{
		if ($this->idEmpresa === null) {
			return redirect()->to('erro/loja')->with('error', 'Loja não selecionada');
		}
		return null;
	}
}
