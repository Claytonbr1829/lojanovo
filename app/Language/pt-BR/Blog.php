<?php

return [
    // Geral
    'blog'              => 'Blog',
    'posts'             => 'Posts',
    'post'              => 'Post',
    'categories'        => 'Categorias',
    'category'          => 'Categoria',
    'tags'              => 'Tags',
    'tag'               => 'Tag',
    'comments'          => 'Comentários',
    'comment'           => 'Comentário',
    'author'            => 'Autor',
    'date'              => 'Data',
    'read_more'         => 'Ler mais',
    
    // Campos
    'fields'            => [
        'title'         => 'Título',
        'content'       => 'Conteúdo',
        'excerpt'       => 'Resumo',
        'slug'          => 'URL amigável',
        'status'        => 'Status',
        'featured_image'=> 'Imagem destacada',
        'meta_title'    => 'Meta título',
        'meta_description'=> 'Meta descrição',
        'meta_keywords' => 'Meta palavras-chave',
    ],
    
    // Status
    'status'            => [
        'draft'         => 'Rascunho',
        'published'     => 'Publicado',
        'scheduled'     => 'Agendado',
        'archived'      => 'Arquivado',
        'private'       => 'Privado',
    ],
    
    // Ações
    'actions'           => [
        'create'        => 'Criar post',
        'edit'          => 'Editar post',
        'delete'        => 'Excluir post',
        'publish'       => 'Publicar',
        'unpublish'     => 'Despublicar',
        'archive'       => 'Arquivar',
        'restore'       => 'Restaurar',
        'preview'       => 'Visualizar',
        'share'         => 'Compartilhar',
        'print'         => 'Imprimir',
    ],
    
    // Categorias
    'categories'        => [
        'title'         => 'Categorias',
        'create'        => 'Criar categoria',
        'edit'          => 'Editar categoria',
        'delete'        => 'Excluir categoria',
        'name'          => 'Nome',
        'description'   => 'Descrição',
        'parent'        => 'Categoria pai',
        'slug'          => 'URL amigável',
    ],
    
    // Tags
    'tags'              => [
        'title'         => 'Tags',
        'create'        => 'Criar tag',
        'edit'          => 'Editar tag',
        'delete'        => 'Excluir tag',
        'name'          => 'Nome',
        'slug'          => 'URL amigável',
        'popular'       => 'Tags populares',
        'cloud'         => 'Nuvem de tags',
    ],
    
    // Comentários
    'comments'          => [
        'title'         => 'Comentários',
        'add'           => 'Adicionar comentário',
        'edit'          => 'Editar comentário',
        'delete'        => 'Excluir comentário',
        'approve'       => 'Aprovar',
        'unapprove'     => 'Desaprovar',
        'spam'          => 'Marcar como spam',
        'reply'         => 'Responder',
        'name'          => 'Nome',
        'email'         => 'E-mail',
        'website'       => 'Website',
        'message'       => 'Mensagem',
        'status'        => [
            'pending'   => 'Pendente',
            'approved'  => 'Aprovado',
            'spam'      => 'Spam',
            'trash'     => 'Lixeira',
        ],
    ],
    
    // Pesquisa
    'search'            => [
        'title'         => 'Pesquisar no blog',
        'placeholder'   => 'Digite sua pesquisa...',
        'results'       => 'Resultados da pesquisa',
        'no_results'    => 'Nenhum resultado encontrado',
        'search_by'     => 'Pesquisar por',
        'categories'    => 'Categorias',
        'tags'          => 'Tags',
        'date'          => 'Data',
    ],
    
    // Arquivo
    'archive'           => [
        'title'         => 'Arquivo',
        'by_date'       => 'Por data',
        'by_category'   => 'Por categoria',
        'by_author'     => 'Por autor',
        'by_tag'        => 'Por tag',
        'monthly'       => 'Arquivo mensal',
        'yearly'        => 'Arquivo anual',
    ],
    
    // Compartilhamento
    'sharing'           => [
        'title'         => 'Compartilhar',
        'facebook'      => 'Facebook',
        'twitter'       => 'Twitter',
        'linkedin'      => 'LinkedIn',
        'whatsapp'      => 'WhatsApp',
        'email'         => 'E-mail',
        'print'         => 'Imprimir',
    ],
    
    // Relacionados
    'related'           => [
        'title'         => 'Posts relacionados',
        'by_category'   => 'Posts da mesma categoria',
        'by_tag'        => 'Posts com as mesmas tags',
        'by_author'     => 'Posts do mesmo autor',
        'popular'       => 'Posts populares',
        'recent'        => 'Posts recentes',
    ],
    
    // Newsletter
    'newsletter'        => [
        'title'         => 'Newsletter',
        'subscribe'     => 'Inscrever-se',
        'unsubscribe'   => 'Cancelar inscrição',
        'email'         => 'E-mail',
        'name'          => 'Nome',
        'success'       => 'Inscrição realizada com sucesso',
        'error'         => 'Erro ao realizar inscrição',
    ],
    
    // RSS
    'rss'               => [
        'title'         => 'RSS Feed',
        'subscribe'     => 'Inscrever-se no RSS',
        'latest'        => 'Últimos posts',
        'category'      => 'Posts da categoria',
        'author'        => 'Posts do autor',
    ],
    
    // Mensagens
    'messages'          => [
        'created'       => 'Post criado com sucesso',
        'updated'       => 'Post atualizado com sucesso',
        'deleted'       => 'Post excluído com sucesso',
        'published'     => 'Post publicado com sucesso',
        'unpublished'   => 'Post despublicado com sucesso',
        'archived'      => 'Post arquivado com sucesso',
        'restored'      => 'Post restaurado com sucesso',
        'comment_added' => 'Comentário adicionado com sucesso',
        'comment_approved'=> 'Comentário aprovado com sucesso',
        'comment_deleted'=> 'Comentário excluído com sucesso',
    ],
    
    // Erros
    'errors'            => [
        'create_failed' => 'Erro ao criar post',
        'update_failed' => 'Erro ao atualizar post',
        'delete_failed' => 'Erro ao excluir post',
        'publish_failed'=> 'Erro ao publicar post',
        'not_found'     => 'Post não encontrado',
        'comment_failed'=> 'Erro ao adicionar comentário',
        'spam_detected' => 'Comentário detectado como spam',
    ],
]; 