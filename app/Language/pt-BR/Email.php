<?php

return [
    // Geral
    'email'             => 'E-mail',
    'subject'           => 'Assunto',
    'message'           => 'Mensagem',
    'send'              => 'Enviar',
    'preview'           => 'Visualizar',
    'template'          => 'Template',
    
    // Cabeçalhos
    'headers'           => [
        'from'          => 'De',
        'to'            => 'Para',
        'cc'            => 'CC',
        'bcc'           => 'BCC',
        'reply_to'      => 'Responder para',
        'subject'       => 'Assunto',
    ],
    
    // Templates de e-mail
    'templates'         => [
        // Conta
        'welcome'       => 'Bem-vindo à nossa loja',
        'account_confirmation' => 'Confirmação de conta',
        'password_reset'=> 'Redefinição de senha',
        'email_change'  => 'Alteração de e-mail',
        
        // Pedidos
        'order_confirmation' => 'Confirmação de pedido',
        'order_shipped'  => 'Pedido enviado',
        'order_delivered'=> 'Pedido entregue',
        'order_cancelled'=> 'Pedido cancelado',
        'order_refunded'=> 'Pedido reembolsado',
        
        // Pagamentos
        'payment_confirmation' => 'Confirmação de pagamento',
        'payment_failed' => 'Pagamento falhou',
        'payment_refunded'=> 'Pagamento reembolsado',
        'invoice'       => 'Fatura',
        
        // Marketing
        'newsletter'    => 'Newsletter',
        'promotion'     => 'Promoção',
        'special_offer' => 'Oferta especial',
        'abandoned_cart'=> 'Carrinho abandonado',
        'product_restock' => 'Produto de volta ao estoque',
        
        // Outros
        'contact_form'  => 'Formulário de contato',
        'support_ticket'=> 'Ticket de suporte',
        'review_request'=> 'Solicitação de avaliação',
    ],
    
    // Variáveis de template
    'variables'         => [
        'site_name'     => 'Nome do site',
        'site_url'      => 'URL do site',
        'user_name'     => 'Nome do usuário',
        'user_email'    => 'E-mail do usuário',
        'order_number'  => 'Número do pedido',
        'order_date'    => 'Data do pedido',
        'order_total'   => 'Total do pedido',
        'tracking_number'=> 'Número de rastreamento',
        'reset_link'    => 'Link de redefinição',
        'confirm_link'  => 'Link de confirmação',
    ],
    
    // Status de envio
    'status'            => [
        'sent'          => 'Enviado',
        'failed'        => 'Falhou',
        'pending'       => 'Pendente',
        'queued'        => 'Na fila',
        'delivered'     => 'Entregue',
        'opened'        => 'Aberto',
        'clicked'       => 'Clicado',
        'bounced'       => 'Devolvido',
        'spam'          => 'Spam',
    ],
    
    // Configurações
    'settings'          => [
        'from_name'     => 'Nome do remetente',
        'from_email'    => 'E-mail do remetente',
        'reply_to'      => 'Responder para',
        'smtp_host'     => 'Servidor SMTP',
        'smtp_port'     => 'Porta SMTP',
        'smtp_user'     => 'Usuário SMTP',
        'smtp_pass'     => 'Senha SMTP',
        'smtp_crypto'   => 'Criptografia SMTP',
    ],
    
    // Logs
    'logs'              => [
        'sent_to'       => 'Enviado para',
        'sent_date'     => 'Data de envio',
        'status'        => 'Status',
        'subject'       => 'Assunto',
        'template'      => 'Template',
        'error'         => 'Erro',
    ],
    
    // Mensagens de erro
    'errors'            => [
        'send_failed'   => 'Falha ao enviar e-mail',
        'invalid_email' => 'E-mail inválido',
        'missing_field'=> 'Campo obrigatório ausente',
        'template_not_found' => 'Template não encontrado',
        'smtp_error'    => 'Erro de conexão SMTP',
    ],
    
    // Mensagens de sucesso
    'success'           => [
        'sent'          => 'E-mail enviado com sucesso',
        'queued'        => 'E-mail adicionado à fila',
        'template_saved'=> 'Template salvo com sucesso',
    ],
    
    // Validação
    'validation'        => [
        'required'      => 'O campo {0} é obrigatório',
        'valid_email'   => 'O campo {0} deve conter um endereço de e-mail válido',
        'min_length'    => 'O campo {0} deve ter pelo menos {1} caracteres',
        'max_length'    => 'O campo {0} não pode exceder {1} caracteres',
    ],
    
    // Botões
    'buttons'           => [
        'send'          => 'Enviar',
        'preview'       => 'Visualizar',
        'save'          => 'Salvar',
        'cancel'        => 'Cancelar',
        'test'          => 'Enviar teste',
    ],
    
    // Outros
    'attachments'       => 'Anexos',
    'queue'             => 'Fila de e-mails',
    'bulk_send'         => 'Envio em massa',
    'test_email'        => 'E-mail de teste',
    'preview_text'      => 'Texto de visualização',
]; 