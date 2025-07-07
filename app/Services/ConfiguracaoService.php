<?php
namespace App\Services;

use App\Models\AparenciaModel;
use Config\Services;

class ConfiguracaoService
{
    private $aparenciaModel;
    private $settings;
    private $idEmpresa;
    
    public function __construct()
    {
        $this->aparenciaModel = new AparenciaModel();
        // No CodeIgniter 4, a configuração é carregada de forma diferente
        $this->idEmpresa = 1; // Valor padrão para testes
        
        // Tenta obter o ID da empresa da sessão
        $session = Services::session();
        if ($session->has('empresa_id') && !empty($session->get('empresa_id'))) {
            $this->idEmpresa = $session->get('empresa_id');
        }
    }
    
    /**
     * Obtém as configurações de aparência da loja
     *
     * @return array Configurações de aparência
     */
    public function getAparencia(): array
    {
        return $this->aparenciaModel->getConfiguracoes();
    }
    
    /**
     * Obtém as configurações gerais da loja
     *
     * @return array Configurações gerais
     */
    public function getConfiguracoesLoja(): array
    {
        return $this->aparenciaModel->getConfiguracoesLoja();
    }
    
    /**
     * Gera o CSS personalizado com base nas configurações de aparência
     *
     * @return string CSS personalizado
     */
    public function gerarCSS(): string
    {
        $aparencia = $this->getAparencia();
        
        $css = ":root {\n";
        $css .= "  --primary-color: " . ($aparencia['cor_primaria'] ?? '#D32F2F') . ";\n";
        $css .= "  --secondary-color: " . ($aparencia['cor_secundaria'] ?? '#455A64') . ";\n";
        $css .= "  --text-color: " . ($aparencia['cor_texto'] ?? '#333333') . ";\n";
        $css .= "  --background-color: " . ($aparencia['cor_fundo'] ?? '#f0f0f0') . ";\n";
        $css .= "  --accent-color: " . ($aparencia['cor_destaque'] ?? '#FFC107') . ";\n";
        $css .= "  --button-primary-color: " . ($aparencia['cor_botao_primario'] ?? '#D32F2F') . ";\n";
        $css .= "  --button-secondary-color: " . ($aparencia['cor_botao_secundario'] ?? '#455A64') . ";\n";
        $css .= "  --font-family: " . ($aparencia['fonte_principal'] ?? 'Roboto, sans-serif') . ";\n";
        $css .= "}";
        
        return $css;
    }
    
    /**
     * Obtém o CSS para o modo escuro, se ativado
     *
     * @return string|null CSS para modo escuro, ou null se não ativado
     */
    public function getModoCss(): ?string
    {
        $aparencia = $this->getAparencia();
        
        if (isset($aparencia['ativar_modo_escuro']) && $aparencia['ativar_modo_escuro']) {
            return ":root {\n" .
                "  --primary-color: #D32F2F;\n" .
                "  --secondary-color: #607D8B;\n" .
                "  --text-color: #f5f5f5;\n" .
                "  --background-color: #121212;\n" .
                "  --accent-color: #FFC107;\n" .
                "  --button-primary-color: #D32F2F;\n" .
                "  --button-secondary-color: #607D8B;\n" .
                "}\n" .
                "body {\n" .
                "  background-color: #121212;\n" .
                "  color: #f5f5f5;\n" .
                "}\n" .
                ".card, .bg-white {\n" .
                "  background-color: #1E1E1E !important;\n" .
                "  color: #f5f5f5;\n" .
                "}\n" .
                ".text-dark {\n" .
                "  color: #f5f5f5 !important;\n" .
                "}\n";
        }
        
        return null;
    }
    
    /**
     * Obtém a configuração do banner principal
     *
     * @return array Dados do banner principal
     */
    public function getBannerPrincipal(): array
    {
        $aparencia = $this->getAparencia();
        
        return [
            'imagem' => $aparencia['banner_principal'] ?? 'banner-default.jpg',
            'texto' => $aparencia['texto_banner_principal'] ?? 'Bem-vindo à loja SwapShop'
        ];
    }
    
    /**
     * Obtém a configuração de logo
     *
     * @return string|null Caminho da logo
     */
    public function getLogo(): ?string
    {
        $aparencia = $this->getAparencia();
        return $aparencia['logo'] ?? null;
    }
} 