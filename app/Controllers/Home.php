<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Models\CategoriaModel;
use App\Models\MarcaParceiraModel;
use App\Models\DepoimentoModel;
use App\Models\AparenciaModel;

class Home extends BaseController
{
	/**
	 * Exibe a página inicial da loja
	 */
	public function index()
	{
		try {
			// Carrega os modelos necessários
			$produtoModel = new ProdutoModel();
			$categoriaModel = new CategoriaModel();
			$marcaParceiraModel = new MarcaParceiraModel();
			$depoimentoModel = new DepoimentoModel();
			$aparenciaModel = new AparenciaModel();

			// Obtém os produtos em destaque
			$produtosDestaque = $produtoModel->getProdutosDestaque();

			// Obtém os produtos mais vendidos
			$produtosMaisVendidos = $produtoModel->getProdutosMaisVendidos();

			// Obtém as novidades
			$produtosNovos = $produtoModel->getProdutosNovos();

			// Obtém as categorias principais
			//$categorias = $categoriaModel->getCategorias(true, 6);
			$categorias = $categoriaModel->findAll();
			// Obtém as marcas parceiras
			$marcasParceiras = $marcaParceiraModel->getMarcasParceiras();

			// Obtém os depoimentos
			$depoimentos = $depoimentoModel->getDepoimentos();

			// Obtém as configurações de aparência
			$aparencia = $aparenciaModel->getConfiguracoes();

			// Prepara os dados para a view
			$data = [
				'title' => $aparencia['titulo_site'] ?? 'Página Inicial',
				'descricao_site' => $aparencia['descricao_site'] ?? '',
				'palavras_chave' => $aparencia['palavras_chave'] ?? '',
				'produtosDestaque' => $produtosDestaque,
				'produtosMaisVendidos' => $produtosMaisVendidos,
				'produtosNovos' => $produtosNovos,
				'categorias' => $categorias,
				'marcasParceiras' => $marcasParceiras,
				'depoimentos' => $depoimentos,
				'aparencia' => $aparencia
			];
			// Renderiza a view
			return $this->renderView('home', $data);

		} catch (\Exception $e) {
			// Registra o erro no log
			log_message('error', 'Erro na página inicial: ' . $e->getMessage());

			// Em ambiente de produção, mostrar uma página mais amigável
			if (ENVIRONMENT === 'production') {
				// Dados para a view de erro
				$data = [
					'title' => 'Oops! Algo deu errado',
					'message' => 'Estamos enfrentando dificuldades técnicas. Por favor, tente novamente mais tarde.'
				];

				// Renderiza a view de erro
				return $this->renderView('errors/html/error_exception', $data);
			}

			// Em ambiente de desenvolvimento, lança a exceção para mostrar detalhes
			throw $e;
		}
	}


}
