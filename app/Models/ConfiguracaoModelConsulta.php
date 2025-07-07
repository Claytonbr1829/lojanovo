<?php
namespace App\Models;

use Exception;

class ConfiguracaoModelConsulta extends ConfiguracaoModelBase
{
    /**
     * Obtém as configurações da loja virtual
     *
     * @param int|null $idEmpresa ID da empresa
     * @return array Configurações da loja
     */
    public function getConfiguracoes($idEmpresa = null): array
    {
        try {
            // Se não foi informado o ID da empresa, usa o padrão
            if ($idEmpresa === null) {
                $idEmpresa = $this->idEmpresa;
            }

            // Busca configurações gerais
            $builder = $this->db->table('loja_config c');
            $builder->select('c.*, a.cor_primaria, a.cor_secundaria, a.cor_texto, a.cor_fundo, a.fonte, a.logo, a.banner_principal');
            $builder->select('s.meta_title as meta_titulo, s.meta_description as meta_descricao, s.og_title, s.og_description, s.og_image');
            $builder->join('loja_aparencia a', 'c.id_empresa = a.id_empresa', 'left');
            $builder->join('loja_seo s', 'c.id_empresa = s.id_empresa', 'left');
            $builder->where('c.id_empresa', $idEmpresa);
            
            $query = $builder->get();
            $config = $query->getRowArray();
            
            if (!$config) {
                // Retorna configurações padrão se não encontrar
                return $this->getConfiguracoesDefault();
            }
            
            // Garantir caminho de imagens ou padrão
            if (empty($config['logo'])) {
                $config['logo'] = 'logo-default.png';
            }
            if (empty($config['banner_principal'])) {
                $config['banner_principal'] = 'banner-default.jpg';
            }
            if (empty($config['og_image'])) {
                $config['og_image'] = 'og-image-default.jpg';
            }
            
            // Adiciona valores padrão para novos campos se não existirem
            $config['mostrar_mais_vendidos'] = $config['mostrar_mais_vendidos'] ?? 1;
            $config['mostrar_marcas_parceiras'] = $config['mostrar_marcas_parceiras'] ?? 1;
            $config['mostrar_assine_Newletter'] = $config['mostrar_assine_Newletter'] ?? 1;
            
            return $config;
        } catch (Exception $e) {
            log_message('error', "Erro ao buscar configurações: " . $e->getMessage());
            
            // Retorna configurações padrão em caso de erro
            return $this->getConfiguracoesDefault();
        }
    }
    
    /**
     * Obtém as configurações da loja para uso no CSS dinâmico
     *
     * @return array Configurações formatadas para o CSS dinâmico
     */
    public function obterConfiguracoes(): array
    {
        $config = $this->getConfiguracoes();
        
        return [
            'cor_primaria' => $config['cor_primaria'] ?? '#007bff',
            'cor_secundaria' => $config['cor_secundaria'] ?? '#6c757d',
            'cor_texto' => $config['cor_texto'] ?? '#212529',
            'cor_fundo' => $config['cor_fundo'] ?? '#ffffff',
            'fonte_principal' => $config['fonte'] ?? 'Roboto, sans-serif'
        ];
    }
    
    /**
     * Obtém as configurações de SEO da loja
     *
     * @return array Configurações de SEO
     */
    public function obterConfiguracoesSEO(): array
    {
        $config = $this->getConfiguracoes();
        
        return [
            'meta_titulo' => $config['meta_titulo'] ?? 'SwapShop - Sua loja virtual',
            'meta_descricao' => $config['meta_descricao'] ?? 'SwapShop - Sua loja virtual completa',
            'og_title' => $config['og_title'] ?? $config['meta_titulo'] ?? 'SwapShop - Sua loja virtual',
            'og_description' => $config['og_description'] ?? $config['meta_descricao'] ?? 'SwapShop - Sua loja virtual completa',
            'og_image' => $config['og_image'] ?? 'og-image-default.jpg'
        ];
    }
    
    /**
     * Obtém as configurações de contato da loja
     *
     * @return array Configurações de contato
     */
    public function obterConfiguracoesContato(): array
    {
        $config = $this->getConfiguracoes();
        
        return [
            'telefone_contato' => $config['telefone_contato'] ?? '',
            'email_contato' => $config['email_contato'] ?? '',
            'endereco_loja' => $config['endereco_loja'] ?? '',
            'horario_funcionamento' => $config['horario_funcionamento'] ?? '',
            'whatsapp' => $config['whatsapp'] ?? ''
        ];
    }
    
    /**
     * Obtém as configurações de redes sociais da loja
     *
     * @return array Configurações de redes sociais
     */
    public function obterConfiguracoesRedesSociais(): array
    {
        $config = $this->getConfiguracoes();
        
        return [
            'facebook' => $config['facebook'] ?? '',
            'instagram' => $config['instagram'] ?? '',
            'twitter' => $config['twitter'] ?? '',
            'youtube' => $config['youtube'] ?? '',
            'linkedin' => $config['linkedin'] ?? '',
            'whatsapp' => $config['whatsapp'] ?? ''
        ];
    }
} 