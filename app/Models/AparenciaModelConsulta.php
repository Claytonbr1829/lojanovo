<?php

namespace App\Models;

use PDO;
use Exception;

class AparenciaModelConsulta extends AparenciaModelBase
{
    private $configuracoes = null;
    
    /**
     * Obtém as configurações de aparência da loja
     *
     * @param int|null $idEmpresa ID da empresa (opcional)
     * @return array Configurações de aparência
     */
    public function getConfiguracoes($idEmpresa = null): array
    {
        // Se já tiver carregado as configurações, retorna
        if ($this->configuracoes !== null) {
            return $this->configuracoes;
        }

        try {
            // Se não foi informado o ID da empresa, usa o da sessão
            if ($idEmpresa === null) {
                $idEmpresa = $this->idEmpresa;
            }

            // Usando Query Builder do CodeIgniter
            $query = $this->db->table('loja_aparencia')
                             ->where('id_empresa', $idEmpresa)
                             ->get();
            
            $config = $query->getRow(0, 'array');
            
            if (!$config) {
                // Retorna configurações padrão se não encontrar
                $this->configuracoes = $this->getConfiguracoesDefault();
                return $this->configuracoes;
            }
            
            // Garantir caminho de imagens ou padrão
            if (empty($config['logo'])) {
                $config['logo'] = 'logo-default.png';
            }
            if (empty($config['banner_principal'])) {
                $config['banner_principal'] = 'banner-default.jpg';
            }
            
            $this->configuracoes = $config;
            return $config;
        } catch (Exception $e) {
            error_log("Erro ao buscar configurações de aparência: " . $e->getMessage());
            
            // Retorna configurações padrão em caso de erro
            $this->configuracoes = $this->getConfiguracoesDefault();
            return $this->configuracoes;
        }
    }

    /**
     * Obtém as configurações gerais da loja
     *
     * @param int|null $idEmpresa ID da empresa (opcional)
     * @return array Configurações da loja
     */
    public function getConfiguracoesLoja($idEmpresa = null): array
    {
        try {
            // Se não foi informado o ID da empresa, usa o da sessão
            if ($idEmpresa === null) {
                $idEmpresa = $this->idEmpresa;
            }

            // Usando Query Builder do CodeIgniter
            $query = $this->db->table('loja_config')
                             ->where('id_empresa', $idEmpresa)
                             ->get();
            
            $config = $query->getRow(0, 'array');
            
            if (!$config) {
                // Retorna configurações padrão se não encontrar
                return [
                    'nome_loja' => 'SwapShop',
                    'descricao' => 'SwapShop - Sua loja virtual completa',
                    'email_contato' => 'contato@swapshop.com.br',
                    'telefone' => '(31) 3025-0000',
                    'horario_funcionamento' => 'Seg a Sex 08:00 às 18:00h',
                    'endereco' => 'Av.Bias Fortes,170, Brite-MG',
                    'mostrar_precos' => 1,
                    'mostrar_depoimentos' => 1,
                    'mostrar_contato_rodape' => 1
                ];
            }
            
            return $config;
        } catch (Exception $e) {
            error_log("Erro ao buscar configurações da loja: " . $e->getMessage());
            
            // Retorna configurações padrão em caso de erro
            return [
                'nome_loja' => 'SwapShop',
                'descricao' => 'SwapShop - Sua loja virtual completa',
                'email_contato' => 'contato@swapshop.com.br',
                'telefone' => '(31) 99236-8155',
                'horario_funcionamento' => 'Seg a Sex 08:00 às 18:00h',
                'endereco' => 'Av.Bias Fortes,170, Brite-MG',
                'mostrar_precos' => 1,
                'mostrar_depoimentos' => 1,
                'mostrar_contato_rodape' => 1
            ];
        }
    }
} 