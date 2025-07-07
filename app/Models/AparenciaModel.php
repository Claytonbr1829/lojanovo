<?php
namespace App\Models;

use PDO;
use Exception;

class AparenciaModel extends AparenciaModelBase
{
    private $modelConsulta;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->modelConsulta = new AparenciaModelConsulta();
    }
    
    /**
     * Obtém as configurações de aparência da loja
     *
     * @param int|null $idEmpresa ID da empresa (opcional)
     * @return array Configurações de aparência
     */
    public function getConfiguracoes($idEmpresa = null): array
    {
        return $this->modelConsulta->getConfiguracoes($idEmpresa);
    }
    
    /**
     * Obtém as configurações gerais da loja
     *
     * @param int|null $idEmpresa ID da empresa (opcional)
     * @return array Configurações da loja
     */
    public function getConfiguracoesLoja($idEmpresa = null): array
    {
        return $this->modelConsulta->getConfiguracoesLoja($idEmpresa);
    }
    
    /**
     * Retorna o CSS dinâmico baseado nas configurações
     *
     * @param int $idEmpresa ID da empresa
     * @return string CSS dinamicamente gerado
     */
    public function getCssDinamico($idEmpresa = 1)
    {
        try {
            $aparencia = $this->getConfiguracoes($idEmpresa);
            
            // Gera o CSS com base nas configurações
            $css = ":root {\n";
            $css .= "    --cor-primaria: " . ($aparencia['cor_primaria'] ?: '#007bff') . ";\n";
            $css .= "    --cor-secundaria: " . ($aparencia['cor_secundaria'] ?: '#6c757d') . ";\n";
            $css .= "    --cor-texto: " . ($aparencia['cor_texto'] ?: '#212529') . ";\n";
            $css .= "    --cor-fundo: " . ($aparencia['cor_fundo'] ?: '#ffffff') . ";\n";
            $css .= "    --fonte-principal: '" . ($aparencia['fonte'] ?: 'Roboto') . ", sans-serif';\n";
            $css .= "}\n\n";
            
            $css .= "body {\n";
            $css .= "    font-family: var(--fonte-principal);\n";
            $css .= "    color: var(--cor-texto);\n";
            $css .= "    background-color: var(--cor-fundo);\n";
            $css .= "}\n\n";
            
            $css .= ".btn-primary {\n";
            $css .= "    background-color: var(--cor-primaria);\n";
            $css .= "    border-color: var(--cor-primaria);\n";
            $css .= "}\n\n";
            
            $css .= ".btn-secondary {\n";
            $css .= "    background-color: var(--cor-secundaria);\n";
            $css .= "    border-color: var(--cor-secundaria);\n";
            $css .= "}\n\n";
            
            $css .= ".bg-primary {\n";
            $css .= "    background-color: var(--cor-primaria) !important;\n";
            $css .= "}\n\n";
            
            $css .= ".bg-secondary {\n";
            $css .= "    background-color: var(--cor-secundaria) !important;\n";
            $css .= "}\n\n";
            
            $css .= ".text-primary {\n";
            $css .= "    color: var(--cor-primaria) !important;\n";
            $css .= "}\n\n";
            
            // Adiciona o CSS personalizado se existir
            if (!empty($aparencia['css_personalizado'])) {
                $css .= "/* CSS Personalizado */\n";
                $css .= $aparencia['css_personalizado'] . "\n";
            }
            
            return $css;
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar CSS dinâmico: ' . $e->getMessage());
            
            // Retorna CSS básico em caso de erro
            return ":root { --cor-primaria: #007bff; --cor-secundaria: #6c757d; --cor-texto: #212529; --cor-fundo: #ffffff; }";
        }
    }
    
    /**
     * Atualiza as configurações de aparência
     *
     * @param array $data Dados de aparência
     * @param int $idEmpresa ID da empresa
     * @return bool Resultado da operação
     */
    public function atualizarAparencia($data, $idEmpresa = 1)
    {
        try {
            // Verifica se já existe uma configuração para esta empresa
            $existente = $this->where('id_empresa', $idEmpresa)->first();
            
            if ($existente) {
                // Atualiza registro existente
                return $this->update($existente['id_aparencia'], array_merge($data, ['id_empresa' => $idEmpresa]));
            } else {
                // Cria novo registro
                return $this->insert(array_merge($data, ['id_empresa' => $idEmpresa]));
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar aparência: ' . $e->getMessage());
            return false;
        }
    }
} 