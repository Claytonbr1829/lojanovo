<?php

return [
    // Erros HTTP
    'http'              => [
        '400'           => 'Requisição inválida',
        '401'           => 'Não autorizado',
        '403'           => 'Acesso negado',
        '404'           => 'Página não encontrada',
        '405'           => 'Método não permitido',
        '408'           => 'Tempo de requisição esgotado',
        '429'           => 'Muitas requisições',
        '500'           => 'Erro interno do servidor',
        '502'           => 'Gateway inválido',
        '503'           => 'Serviço indisponível',
        '504'           => 'Tempo de gateway esgotado',
    ],
    
    // Mensagens de erro HTTP
    'http_messages'     => [
        '400'           => 'A requisição não pôde ser processada devido a um erro do cliente.',
        '401'           => 'Você precisa estar autenticado para acessar este recurso.',
        '403'           => 'Você não tem permissão para acessar este recurso.',
        '404'           => 'A página que você está procurando não foi encontrada.',
        '405'           => 'O método de requisição não é permitido para este recurso.',
        '408'           => 'O servidor esgotou o tempo de espera da requisição.',
        '429'           => 'Você enviou muitas requisições. Por favor, aguarde um momento.',
        '500'           => 'Ocorreu um erro interno no servidor.',
        '502'           => 'O servidor recebeu uma resposta inválida do servidor upstream.',
        '503'           => 'O serviço está temporariamente indisponível.',
        '504'           => 'O servidor upstream não respondeu a tempo.',
    ],
    
    // Erros de banco de dados
    'database'          => [
        'connection'    => 'Erro de conexão com o banco de dados',
        'query'         => 'Erro na consulta ao banco de dados',
        'transaction'   => 'Erro na transação do banco de dados',
        'constraint'    => 'Violação de restrição do banco de dados',
        'foreign_key'   => 'Violação de chave estrangeira',
        'unique'        => 'Violação de chave única',
        'not_found'     => 'Registro não encontrado',
        'insert'        => 'Erro ao inserir registro',
        'update'        => 'Erro ao atualizar registro',
        'delete'        => 'Erro ao excluir registro',
    ],
    
    // Erros de validação
    'validation'        => [
        'required'      => 'O campo {0} é obrigatório',
        'min_length'    => 'O campo {0} deve ter pelo menos {1} caracteres',
        'max_length'    => 'O campo {0} não pode exceder {1} caracteres',
        'exact_length'  => 'O campo {0} deve ter exatamente {1} caracteres',
        'valid_email'   => 'O campo {0} deve conter um endereço de e-mail válido',
        'valid_url'     => 'O campo {0} deve conter uma URL válida',
        'valid_ip'      => 'O campo {0} deve conter um endereço IP válido',
        'alpha'         => 'O campo {0} deve conter apenas letras',
        'alpha_numeric' => 'O campo {0} deve conter apenas letras e números',
        'alpha_dash'    => 'O campo {0} deve conter apenas letras, números, underscores e traços',
        'numeric'       => 'O campo {0} deve conter apenas números',
        'integer'       => 'O campo {0} deve conter um número inteiro',
        'decimal'       => 'O campo {0} deve conter um número decimal',
        'matches'       => 'O campo {0} não corresponde ao campo {1}',
    ],
    
    // Erros de arquivo
    'file'              => [
        'upload'        => 'Erro ao fazer upload do arquivo',
        'size'          => 'O arquivo excede o tamanho máximo permitido',
        'extension'     => 'Tipo de arquivo não permitido',
        'not_found'     => 'Arquivo não encontrado',
        'not_readable'  => 'Arquivo não pode ser lido',
        'not_writable'  => 'Arquivo não pode ser escrito',
        'not_deleted'   => 'Arquivo não pode ser excluído',
        'not_moved'     => 'Arquivo não pode ser movido',
        'not_copied'    => 'Arquivo não pode ser copiado',
    ],
    
    // Erros de autenticação
    'auth'              => [
        'invalid_login' => 'E-mail ou senha inválidos',
        'invalid_token' => 'Token inválido ou expirado',
        'unauthorized'  => 'Usuário não autorizado',
        'not_logged_in' => 'Você precisa estar logado para acessar este recurso',
        'already_logged_in' => 'Você já está logado',
        'account_locked' => 'Sua conta foi bloqueada',
        'account_disabled' => 'Sua conta foi desativada',
    ],
    
    // Erros de sessão
    'session'           => [
        'invalid'       => 'Sessão inválida',
        'expired'       => 'Sessão expirada',
        'not_found'     => 'Sessão não encontrada',
    ],
    
    // Erros de cache
    'cache'             => [
        'not_found'     => 'Cache não encontrado',
        'not_writable'  => 'Cache não pode ser escrito',
        'not_readable'  => 'Cache não pode ser lido',
    ],
    
    // Erros de e-mail
    'email'             => [
        'invalid_address' => 'Endereço de e-mail inválido',
        'send_failure'   => 'Não foi possível enviar o e-mail',
        'smtp_error'     => 'Erro no servidor SMTP',
    ],
    
    // Erros de pagamento
    'payment'           => [
        'invalid_card'   => 'Cartão inválido',
        'expired_card'   => 'Cartão expirado',
        'insufficient_funds' => 'Saldo insuficiente',
        'card_declined'  => 'Cartão recusado',
        'transaction_failed' => 'Transação falhou',
    ],
    
    // Mensagens genéricas
    'generic'           => [
        'error'         => 'Ocorreu um erro',
        'try_again'     => 'Por favor, tente novamente',
        'contact_support' => 'Entre em contato com o suporte',
        'maintenance'   => 'Sistema em manutenção',
    ],
]; 