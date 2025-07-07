<?php
namespace App\Models;

use PDO;
use Exception;

class ProdutoModelCRUD extends ProdutoModelBase
{
    /**
     * Cria um novo produto
     *
     * @param array $dados Dados do produto
     * @return int ID do produto criado ou 0 em caso de erro
     */
    public function criar(array $dados): int
    {
        try {
            $this->db->beginTransaction();
            
            // Garante que a empresa seja a atual
            $dados['id_empresa'] = $this->idEmpresa;
            
            // Gera o slug se não foi fornecido
            if (empty($dados['slug'])) {
                $dados['slug'] = $this->gerarSlug($dados['nome']);
            }
            
            // Adiciona timestamps
            $agora = date('Y-m-d H:i:s');
            $dados['created_at'] = $agora;
            $dados['updated_at'] = $agora;
            
            // Insere o produto
            $campos = implode(', ', array_keys($dados));
            $placeholders = ':' . implode(', :', array_keys($dados));
            
            $sql = "INSERT INTO {$this->table} ({$campos}) VALUES ({$placeholders})";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($dados as $campo => $valor) {
                $tipo = is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue(":{$campo}", $valor, $tipo);
            }
            
            $stmt->execute();
            
            $idProduto = $this->db->lastInsertId();
            
            // Confirma a transação
            $this->db->commit();
            
            return $idProduto;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Erro ao criar produto: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Atualiza um produto existente
     *
     * @param int $id ID do produto
     * @param array $dados Dados do produto
     * @return bool Sucesso ou falha
     */
    public function atualizar(int $id, array $dados): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Garante que o ID da empresa seja mantido
            unset($dados['id_empresa']);
            
            // Atualiza o timestamp
            $dados['updated_at'] = date('Y-m-d H:i:s');
            
            // Prepara os campos para atualização
            $sets = [];
            foreach (array_keys($dados) as $campo) {
                $sets[] = "{$campo} = :{$campo}";
            }
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->primaryKey} = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            foreach ($dados as $campo => $valor) {
                $tipo = is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue(":{$campo}", $valor, $tipo);
            }
            
            $resultado = $stmt->execute();
            
            // Confirma a transação
            $this->db->commit();
            
            return $resultado;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Erro ao atualizar produto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove um produto
     *
     * @param int $id ID do produto
     * @return bool Sucesso ou falha
     */
    public function excluir(int $id): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Primeiro, verifica se o produto existe e pertence à empresa atual
            $sql = "SELECT COUNT(*) FROM {$this->table} 
                    WHERE {$this->primaryKey} = :id AND id_empresa = :id_empresa";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->fetchColumn() == 0) {
                return false; // Produto não encontrado ou não pertence à empresa
            }
            
            // Remove as imagens adicionais do produto
            $sqlImagens = "DELETE FROM produtos_imagens WHERE id_produto = :id_produto";
            $stmtImagens = $this->db->prepare($sqlImagens);
            $stmtImagens->bindParam(':id_produto', $id, PDO::PARAM_INT);
            $stmtImagens->execute();
            
            // Remove os atributos do produto
            $sqlAtributos = "DELETE FROM produtos_atributos WHERE id_produto = :id_produto";
            $stmtAtributos = $this->db->prepare($sqlAtributos);
            $stmtAtributos->bindParam(':id_produto', $id, PDO::PARAM_INT);
            $stmtAtributos->execute();
            
            // Remove as variações do produto
            $sqlVariacoes = "DELETE FROM produtos_combinados WHERE id_produto = :id_produto";
            $stmtVariacoes = $this->db->prepare($sqlVariacoes);
            $stmtVariacoes->bindParam(':id_produto', $id, PDO::PARAM_INT);
            $stmtVariacoes->execute();
            
            // Finalmente, remove o produto
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            
            // Confirma a transação
            $this->db->commit();
            
            return $resultado;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Erro ao excluir produto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Adiciona uma imagem ao produto
     *
     * @param int $idProduto ID do produto
     * @param string $imagem Nome do arquivo de imagem
     * @param int $ordem Ordem de exibição
     * @return bool Sucesso ou falha
     */
    public function adicionarImagem(int $idProduto, string $imagem, int $ordem = 0): bool
    {
        try {
            $sql = "INSERT INTO produtos_imagens (id_produto, imagem, ordem) VALUES (:id_produto, :imagem, :ordem)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->bindParam(':imagem', $imagem, PDO::PARAM_STR);
            $stmt->bindParam(':ordem', $ordem, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erro ao adicionar imagem ao produto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove uma imagem do produto
     *
     * @param int $idImagem ID da imagem
     * @return bool Sucesso ou falha
     */
    public function removerImagem(int $idImagem): bool
    {
        try {
            $sql = "DELETE FROM produtos_imagens WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $idImagem, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erro ao remover imagem do produto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Adiciona um atributo ao produto
     *
     * @param int $idProduto ID do produto
     * @param int $idAtributo ID do atributo
     * @param string $valor Valor do atributo
     * @return bool Sucesso ou falha
     */
    public function adicionarAtributo(int $idProduto, int $idAtributo, string $valor): bool
    {
        try {
            $sql = "INSERT INTO produtos_atributos (id_produto, id_atributo, valor) 
                    VALUES (:id_produto, :id_atributo, :valor)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->bindParam(':id_atributo', $idAtributo, PDO::PARAM_INT);
            $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erro ao adicionar atributo ao produto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Adiciona uma variação ao produto
     *
     * @param int $idProduto ID do produto
     * @param array $dados Dados da variação
     * @return bool Sucesso ou falha
     */
    public function adicionarVariacao(int $idProduto, array $dados): bool
    {
        try {
            // Garante que o ID do produto seja mantido
            $dados['id_produto'] = $idProduto;
            
            // Prepara a inserção
            $campos = implode(', ', array_keys($dados));
            $placeholders = ':' . implode(', :', array_keys($dados));
            
            $sql = "INSERT INTO produtos_combinados ({$campos}) VALUES ({$placeholders})";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($dados as $campo => $valor) {
                $tipo = is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue(":{$campo}", $valor, $tipo);
            }
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erro ao adicionar variação ao produto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Gera um slug único para o produto
     *
     * @param string $nome Nome do produto
     * @return string Slug gerado
     */
    protected function gerarSlug(string $nome): string
    {
        // Converte para minúsculas e remove acentos
        $slug = strtolower(preg_replace('/[^\w\d]+/', '-', $this->removerAcentos($nome)));
        
        // Remove hífens duplicados
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Remove hífens no início e no fim
        $slug = trim($slug, '-');
        
        // Verifica se o slug já existe
        $slugOriginal = $slug;
        $contador = 1;
        
        while ($this->slugExiste($slug)) {
            $slug = $slugOriginal . '-' . $contador;
            $contador++;
        }
        
        return $slug;
    }
    
    /**
     * Verifica se um slug já existe
     *
     * @param string $slug Slug a verificar
     * @return bool True se existe, False caso contrário
     */
    protected function slugExiste(string $slug): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE slug = :slug AND id_empresa = :id_empresa";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Remove acentos de uma string
     *
     * @param string $string String com acentos
     * @return string String sem acentos
     */
    protected function removerAcentos(string $string): string
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }
        
        $chars = [
            // Decompositions for Latin-1 Supplement
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
        ];
        
        return strtr($string, $chars);
    }
} 