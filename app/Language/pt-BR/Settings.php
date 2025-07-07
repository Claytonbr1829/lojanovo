<?php

return [
    // Geral
    'settings'          => 'Configurações',
    'general'           => 'Geral',
    'store'             => 'Loja',
    'system'            => 'Sistema',
    'save_settings'     => 'Salvar configurações',
    
    // Configurações da loja
    'store_settings'    => [
        'title'         => 'Configurações da loja',
        'name'          => 'Nome da loja',
        'description'   => 'Descrição da loja',
        'email'         => 'E-mail da loja',
        'phone'         => 'Telefone da loja',
        'address'       => 'Endereço da loja',
        'country'       => 'País',
        'state'         => 'Estado',
        'city'          => 'Cidade',
        'zip'           => 'CEP',
        'currency'      => 'Moeda',
        'timezone'      => 'Fuso horário',
        'language'      => 'Idioma',
    ],
    
    // Configurações de produtos
    'product_settings'  => [
        'title'         => 'Configurações de produtos',
        'inventory'     => 'Gerenciar estoque',
        'low_stock'     => 'Alerta de estoque baixo',
        'out_of_stock'  => 'Permitir venda sem estoque',
        'reviews'       => 'Permitir avaliações',
        'ratings'       => 'Permitir classificações',
        'dimensions'    => 'Unidade de dimensão',
        'weight'        => 'Unidade de peso',
    ],
    
    // Configurações de pagamento
    'payment_settings'  => [
        'title'         => 'Configurações de pagamento',
        'currency'      => 'Moeda',
        'tax'           => 'Impostos',
        'methods'       => 'Métodos de pagamento',
        'credit_card'   => 'Cartão de crédito',
        'debit_card'    => 'Cartão de débito',
        'bank_slip'     => 'Boleto bancário',
        'bank_transfer' => 'Transferência bancária',
        'pix'           => 'PIX',
    ],
    
    // Configurações de envio
    'shipping_settings' => [
        'title'         => 'Configurações de envio',
        'methods'       => 'Métodos de envio',
        'calculation'   => 'Cálculo de frete',
        'free_shipping' => 'Frete grátis',
        'min_amount'    => 'Valor mínimo',
        'packaging'     => 'Embalagem',
        'handling'      => 'Manuseio',
    ],
    
    // Configurações de e-mail
    'email_settings'    => [
        'title'         => 'Configurações de e-mail',
        'from_name'     => 'Nome do remetente',
        'from_email'    => 'E-mail do remetente',
        'smtp_host'     => 'Servidor SMTP',
        'smtp_port'     => 'Porta SMTP',
        'smtp_user'     => 'Usuário SMTP',
        'smtp_pass'     => 'Senha SMTP',
        'smtp_secure'   => 'Segurança SMTP',
    ],
    
    // Configurações de notificação
    'notification_settings' => [
        'title'         => 'Configurações de notificação',
        'order_status'  => 'Status do pedido',
        'stock_alerts'  => 'Alertas de estoque',
        'new_customer'  => 'Novo cliente',
        'new_order'     => 'Novo pedido',
        'abandoned_cart'=> 'Carrinho abandonado',
    ],
    
    // Configurações de SEO
    'seo_settings'      => [
        'title'         => 'Configurações de SEO',
        'meta_title'    => 'Meta título',
        'meta_description' => 'Meta descrição',
        'meta_keywords' => 'Meta palavras-chave',
        'robots'        => 'Robots.txt',
        'sitemap'       => 'Sitemap',
        'analytics'     => 'Google Analytics',
    ],
    
    // Configurações de segurança
    'security_settings' => [
        'title'         => 'Configurações de segurança',
        'ssl'           => 'SSL',
        'captcha'       => 'CAPTCHA',
        'password_policy' => 'Política de senha',
        'login_attempts'=> 'Tentativas de login',
        'session_timeout' => 'Tempo limite da sessão',
        'maintenance'   => 'Modo de manutenção',
    ],
    
    // Configurações de API
    'api_settings'      => [
        'title'         => 'Configurações de API',
        'enabled'       => 'API habilitada',
        'key'           => 'Chave da API',
        'secret'        => 'Segredo da API',
        'token'         => 'Token da API',
        'webhook'       => 'Webhook URL',
    ],
    
    // Configurações de backup
    'backup_settings'   => [
        'title'         => 'Configurações de backup',
        'auto_backup'   => 'Backup automático',
        'frequency'     => 'Frequência',
        'retention'     => 'Retenção',
        'storage'       => 'Armazenamento',
        'compress'      => 'Compressão',
    ],
    
    // Configurações de cache
    'cache_settings'    => [
        'title'         => 'Configurações de cache',
        'enabled'       => 'Cache habilitado',
        'driver'        => 'Driver de cache',
        'lifetime'      => 'Tempo de vida',
        'prefix'        => 'Prefixo',
    ],
    
    // Mensagens
    'messages'          => [
        'saved'         => 'Configurações salvas com sucesso',
        'error'         => 'Erro ao salvar configurações',
        'confirm_reset' => 'Tem certeza que deseja restaurar as configurações padrão?',
        'reset_success' => 'Configurações restauradas com sucesso',
    ],
]; 