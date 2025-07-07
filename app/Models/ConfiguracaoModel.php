<?php
namespace App\Models;

class ConfiguracaoModel extends ConfiguracaoModelBase
{
    private $modelConsulta;
    private $modelEstilo;
    
    public function __construct()
    {
        parent::__construct();
        $this->modelConsulta = new ConfiguracaoModelConsulta();
        $this->modelEstilo = new ConfiguracaoModelEstilo();
    }
    
    /**
     * Métodos de consulta de configurações
     */
    
    /**
     * Obtém as configurações da loja
     *
     * @param int|null $idEmpresa ID da empresa
     * @return array
     */
    public function getConfiguracoes(?int $idEmpresa = null): array
    {
        // Se não foi informado, usa o ID da empresa atual
        if ($idEmpresa === null) {
            $idEmpresa = $this->idEmpresa;
        }
        
        try {
            // Busca a configuração da loja
            $builder = $this->builder('loja_config');
            $builder->where('id_empresa', $idEmpresa);
            $query = $builder->get();
            
            if ($query->getNumRows() > 0) {
                return $query->getRowArray();
            }
            
            // Se não encontrou, retorna configuração padrão
            return [
                'nome_loja' => 'Minha Loja',
                'descricao' => 'Loja Virtual',
                'mostrar_precos' => 1,
                'mostrar_depoimentos' => 1,
                'mostrar_contato_rodape' => 1,
                'mostrar_mais_vendidos' => 1,
                'mostrar_marcas_parceiras' => 1,
                'mostrar_assine_Newletter' => 1,
            ];
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar configurações: ' . $e->getMessage());
            
            // Retorna configuração padrão em caso de erro
            return [
                'nome_loja' => 'Minha Loja',
                'descricao' => 'Loja Virtual',
                'mostrar_precos' => 1,
                'mostrar_depoimentos' => 1,
                'mostrar_contato_rodape' => 1,
                'mostrar_mais_vendidos' => 1,
                'mostrar_marcas_parceiras' => 1,
                'mostrar_assine_Newletter' => 1,
            ];
        }
    }
    
    public function obterConfiguracoes(): array
    {
        return $this->modelConsulta->obterConfiguracoes();
    }
    
    public function obterConfiguracoesSEO(): array
    {
        return $this->modelConsulta->obterConfiguracoesSEO();
    }
    
    public function obterConfiguracoesContato(): array
    {
        return $this->modelConsulta->obterConfiguracoesContato();
    }
    
    public function obterConfiguracoesRedesSociais(): array
    {
        return $this->modelConsulta->obterConfiguracoesRedesSociais();
    }
    
    /**
     * Obtém o logo da loja
     *
     * @return string Caminho para o logo da loja
     */
    public function getLogo(): string
    {
        $aparencia = $this->modelConsulta->getConfiguracoes();
        return $aparencia['logo'] ?? 'logo-default.png';
    }
    
    /**
     * Métodos de geração de estilos
     */
    
    public function gerarCSS(): string
    {
        return $this->modelEstilo->gerarCSS();
    }
    
    public function gerarCSSEscuro(): string
    {
        return $this->modelEstilo->gerarCSSEscuro();
    }
} 