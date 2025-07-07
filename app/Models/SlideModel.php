<?php

namespace App\Models;

use CodeIgniter\Model;

class SlideModel extends Model
{
    protected $table         = 'loja_slides';
    protected $primaryKey    = 'id_slide';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id_empresa', 'titulo', 'descricao', 'imagem', 'link', 
        'texto_botao', 'ordem', 'ativo'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'data_criacao';
    protected $updatedField  = 'data_atualizacao';
    
    protected $validationRules = [
        'id_empresa' => 'required|integer',
        'titulo'     => 'permit_empty|string|max_length[100]',
        'descricao'  => 'permit_empty|string|max_length[255]',
        'imagem'     => 'required|string|max_length[255]',
        'link'       => 'permit_empty|string|max_length[255]',
        'ordem'      => 'permit_empty|integer',
        'ativo'      => 'permit_empty|integer|in_list[0,1]',
    ];
    
    protected $validationMessages = [
        'imagem'     => [
            'required' => 'É necessário selecionar uma imagem para o slide.'
        ]
    ];
    
    /**
     * Retorna os slides ativos para o carrossel principal
     *
     * @param int $limit Limite de slides a serem retornados
     * @return array Array de slides
     */
    public function getSlidesAtivos($limit = 5)
    {
        try {
            $slides = $this->where('ativo', 1)
                        ->where('id_empresa', 1) // Ajustar conforme necessário
                        ->orderBy('ordem', 'ASC')
                        ->limit($limit)
                        ->findAll();
            
            // Verifica se tem slides e trata as imagens
            if (empty($slides)) {
                // Retorna um slide padrão se não houver slides configurados
                return [
                    [
                        'id_slide' => 0,
                        'titulo' => 'Bem-vindo à nossa loja',
                        'descricao' => 'Encontre os melhores produtos com os melhores preços',
                        'imagem' => 'slide-default.jpg',
                        'link' => '',
                        'texto_botao' => 'Ver produtos',
                        'ordem' => 1
                    ]
                ];
            }
            
            // Processa os slides
            foreach ($slides as &$slide) {
                // Verifica se a imagem existe
                if (empty($slide['imagem']) || !file_exists(FCPATH . 'uploads/banners/' . $slide['imagem'])) {
                    $slide['imagem'] = 'slide-default.jpg';
                }
            }
            
            return $slides;
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao obter slides: ' . $e->getMessage());
            
            // Retorna um slide padrão em caso de erro
            return [
                [
                    'id_slide' => 0,
                    'titulo' => 'Bem-vindo à nossa loja',
                    'descricao' => 'Encontre os melhores produtos com os melhores preços',
                    'imagem' => 'slide-default.jpg',
                    'link' => '',
                    'texto_botao' => 'Ver produtos',
                    'ordem' => 1
                ]
            ];
        }
    }
    
    /**
     * Retorna um slide específico
     *
     * @param int $id ID do slide
     * @return array|null Dados do slide ou null se não encontrado
     */
    public function getSlide($id)
    {
        try {
            $slide = $this->find($id);
            
            if ($slide) {
                // Verifica se a imagem existe
                if (empty($slide['imagem']) || !file_exists(FCPATH . 'uploads/banners/' . $slide['imagem'])) {
                    $slide['imagem'] = 'slide-default.jpg';
                }
            }
            
            return $slide;
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao obter slide: ' . $e->getMessage());
            return null;
        }
    }
} 