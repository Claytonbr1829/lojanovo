<?php

return [
    // Geral
    'logs'              => 'Logs',
    'log'               => 'Log',
    'view_logs'         => 'Ver logs',
    'clear_logs'        => 'Limpar logs',
    'download_logs'     => 'Baixar logs',
    
    // Tipos de log
    'types'             => [
        'error'         => 'Erro',
        'warning'       => 'Aviso',
        'info'          => 'Informação',
        'debug'         => 'Debug',
        'notice'        => 'Notificação',
        'critical'      => 'Crítico',
        'alert'         => 'Alerta',
        'emergency'     => 'Emergência',
    ],
    
    // Categorias
    'categories'        => [
        'system'        => 'Sistema',
        'security'      => 'Segurança',
        'user'          => 'Usuário',
        'database'      => 'Banco de dados',
        'api'           => 'API',
        'cron'          => 'Cron',
        'email'         => 'E-mail',
        'payment'       => 'Pagamento',
        'order'         => 'Pedido',
    ],
    
    // Campos do log
    'fields'            => [
        'id'            => 'ID',
        'date'          => 'Data',
        'time'          => 'Hora',
        'type'          => 'Tipo',
        'category'      => 'Categoria',
        'message'       => 'Mensagem',
        'details'       => 'Detalhes',
        'file'          => 'Arquivo',
        'line'          => 'Linha',
        'ip'            => 'IP',
        'user'          => 'Usuário',
        'url'           => 'URL',
        'method'        => 'Método',
        'status'        => 'Status',
        'duration'      => 'Duração',
    ],
    
    // Filtros
    'filters'           => [
        'date_range'    => 'Intervalo de datas',
        'start_date'    => 'Data inicial',
        'end_date'      => 'Data final',
        'type'          => 'Tipo',
        'category'      => 'Categoria',
        'user'          => 'Usuário',
        'ip'            => 'IP',
        'search'        => 'Buscar',
    ],
    
    // Ações
    'actions'           => [
        'view'          => 'Visualizar',
        'delete'        => 'Excluir',
        'clear'         => 'Limpar',
        'download'      => 'Baixar',
        'filter'        => 'Filtrar',
        'refresh'       => 'Atualizar',
    ],
    
    // Sistema
    'system_logs'       => [
        'startup'       => 'Inicialização do sistema',
        'shutdown'      => 'Desligamento do sistema',
        'update'        => 'Atualização do sistema',
        'maintenance'   => 'Modo de manutenção',
        'backup'        => 'Backup do sistema',
        'restore'       => 'Restauração do sistema',
        'cache'         => 'Limpeza de cache',
    ],
    
    // Segurança
    'security_logs'     => [
        'login'         => 'Login',
        'logout'        => 'Logout',
        'failed_login'  => 'Tentativa de login falha',
        'password_reset'=> 'Redefinição de senha',
        'access_denied' => 'Acesso negado',
        'ip_blocked'    => 'IP bloqueado',
    ],
    
    // Usuário
    'user_logs'         => [
        'created'       => 'Usuário criado',
        'updated'       => 'Usuário atualizado',
        'deleted'       => 'Usuário excluído',
        'blocked'       => 'Usuário bloqueado',
        'unblocked'     => 'Usuário desbloqueado',
    ],
    
    // Banco de dados
    'database_logs'     => [
        'query'         => 'Consulta',
        'error'         => 'Erro de banco de dados',
        'backup'        => 'Backup do banco de dados',
        'restore'       => 'Restauração do banco de dados',
        'migration'     => 'Migração',
    ],
    
    // API
    'api_logs'          => [
        'request'       => 'Requisição API',
        'response'      => 'Resposta API',
        'error'         => 'Erro API',
        'rate_limit'    => 'Limite de taxa excedido',
    ],
    
    // Mensagens
    'messages'          => [
        'cleared'       => 'Logs limpos com sucesso',
        'deleted'       => 'Log excluído com sucesso',
        'no_logs'       => 'Nenhum log encontrado',
        'confirm_clear' => 'Tem certeza que deseja limpar todos os logs?',
        'confirm_delete'=> 'Tem certeza que deseja excluir este log?',
    ],
    
    // Exportação
    'export'            => [
        'filename'      => 'logs_{date}',
        'formats'       => [
            'csv'       => 'CSV',
            'json'      => 'JSON',
            'txt'       => 'Texto',
        ],
    ],
]; 