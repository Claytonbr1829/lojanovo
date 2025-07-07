<?php

return [
    // Geral
    'checkout'          => 'Finalizar compra',
    'checkout_process'  => 'Processo de compra',
    'checkout_summary'  => 'Resumo do pedido',
    'checkout_steps'    => 'Etapas do pedido',
    'checkout_complete' => 'Compra finalizada',
    'checkout_failed'   => 'Falha na compra',
    
    // Etapas
    'steps'             => [
        'cart'          => 'Carrinho',
        'login'         => 'Login',
        'shipping'      => 'Entrega',
        'payment'       => 'Pagamento',
        'review'        => 'Revisão',
        'complete'      => 'Concluído',
    ],
    
    // Login
    'login'             => [
        'title'         => 'Login',
        'email'         => 'E-mail',
        'password'      => 'Senha',
        'forgot_password'=> 'Esqueceu a senha?',
        'register'      => 'Criar conta',
        'guest'         => 'Continuar como visitante',
        'remember_me'   => 'Lembrar-me',
    ],
    
    // Entrega
    'shipping'          => [
        'title'         => 'Entrega',
        'methods'       => 'Métodos de envio',
        'address'       => 'Endereço de entrega',
        'pickup'        => 'Retirada na loja',
        'calculate'     => 'Calcular frete',
        'free'          => 'Frete grátis',
        'express'       => 'Entrega expressa',
        'standard'      => 'Entrega padrão',
        'economy'       => 'Entrega econômica',
        'estimate'      => 'Previsão de entrega',
        'tracking'      => 'Rastreamento',
    ],
    
    // Endereço
    'address'           => [
        'title'         => 'Endereço',
        'new'           => 'Novo endereço',
        'edit'          => 'Editar endereço',
        'select'        => 'Selecionar endereço',
        'type'          => 'Tipo',
        'street'        => 'Rua',
        'number'        => 'Número',
        'complement'    => 'Complemento',
        'neighborhood'  => 'Bairro',
        'city'          => 'Cidade',
        'state'         => 'Estado',
        'country'       => 'País',
        'zip_code'      => 'CEP',
        'phone'         => 'Telefone',
        'default'       => 'Endereço padrão',
    ],

      
    // Pagamento
    'payment'           => [
        'title'         => 'Pagamento',
        'methods'       => 'Formas de pagamento',
        'credit_card'   => 'Cartão de crédito',
        'debit_card'    => 'Cartão de débito',
        'boleto'        => 'Boleto bancário',
        'pix'           => 'PIX',
        'transfer'      => 'Transferência bancária',
        'installments'  => 'Parcelas',
        'interest_free' => 'Sem juros',
        'with_interest' => 'Com juros',
        'secure'        => 'Pagamento seguro',
        'save_card'     => 'Salvar cartão',
    ],
    
    // Cartão
    'card'              => [
        'title'         => 'Cartão',
        'number'        => 'Número',
        'name'          => 'Nome no cartão',
        'expiry'        => 'Validade',
        'cvv'           => 'CVV',
        'installments'  => 'Parcelas',
        'save'          => 'Salvar cartão',
        'saved'         => 'Cartões salvos',
        'remove'        => 'Remover cartão',
        'brand'         => 'Bandeira',
    ],
    
    // Boleto
    'boleto'            => [
        'title'         => 'Boleto bancário',
        'generate'      => 'Gerar boleto',
        'download'      => 'Baixar boleto',
        'print'         => 'Imprimir boleto',
        'expiry'        => 'Vencimento',
        'bank'          => 'Banco',
        'agency'        => 'Agência',
        'account'       => 'Conta',
        'value'         => 'Valor',
        'instructions'  => 'Instruções',
    ],
    
    // PIX
    'pix'               => [
        'title'         => 'PIX',
        'generate'      => 'Gerar QR Code',
        'copy_code'     => 'Copiar código',
        'expiry'        => 'Vencimento',
        'value'         => 'Valor',
        'instructions'  => 'Instruções',
        'scan'          => 'Escaneie o QR Code',
        'manual'        => 'Pagamento manual',
    ],
    
    // Transferência
    'transfer'          => [
        'title'         => 'Transferência bancária',
        'bank'          => 'Banco',
        'agency'        => 'Agência',
        'account'       => 'Conta',
        'type'          => 'Tipo',
        'value'         => 'Valor',
        'instructions'  => 'Instruções',
        'proof'         => 'Comprovante',
        'upload'        => 'Enviar comprovante',
    ],
    
    // Revisão
    'review'            => [
        'title'         => 'Revisão do pedido',
        'items'         => 'Itens',
        'shipping'      => 'Entrega',
        'payment'       => 'Pagamento',
        'total'         => 'Total',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Desconto',
        'tax'           => 'Impostos',
        'shipping_cost' => 'Frete',
        'final_total'   => 'Total final',
        'terms'         => 'Termos e condições',
        'privacy'       => 'Política de privacidade',
        'confirm'       => 'Confirmar pedido',
        'calcular'      => "Calcular Frete"
    ],
    
    // Conclusão
    'complete'          => [
        'title'         => 'Pedido concluído',
        'order_number'  => 'Número do pedido',
        'thank_you'     => 'Obrigado pela compra',
        'confirmation'  => 'Confirmação enviada',
        'tracking'      => 'Rastreamento',
        'invoice'       => 'Fatura',
        'print'         => 'Imprimir',
        'continue'      => 'Continuar comprando',
    ],
    
    // Mensagens
    'messages'          => [
        'success'       => 'Pedido realizado com sucesso',
        'error'         => 'Erro ao processar pedido',
        'validation'    => 'Erro de validação',
        'payment_failed'=> 'Falha no pagamento',
        'shipping_failed'=> 'Falha no envio',
        'stock_error'   => 'Estoque insuficiente',
        'price_changed' => 'Preço alterado',
        'session_expired'=> 'Sessão expirada',
    ],
    
    // Validação
    'validation'        => [
        'required'      => 'Campo obrigatório',
        'email'         => 'E-mail inválido',
        'numeric'       => 'Deve ser numérico',
        'min'           => 'Valor mínimo',
        'max'           => 'Valor máximo',
        'card_number'   => 'Número do cartão inválido',
        'card_expiry'   => 'Validade inválida',
        'card_cvv'      => 'CVV inválido',
        'zip_code'      => 'CEP inválido',
        'phone'         => 'Telefone inválido',
    ],
    
    // Segurança
    'security'          => [
        'title'         => 'Segurança',
        'ssl'           => 'Conexão segura',
        'encryption'    => 'Criptografia',
        'certified'     => 'Certificado',
        'guarantee'     => 'Garantia',
        'privacy'       => 'Privacidade',
        'terms'         => 'Termos',
        'policy'        => 'Política',
    ],
]; 