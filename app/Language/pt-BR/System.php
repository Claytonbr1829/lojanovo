<?php

return [
    // Geral
    'system'            => 'Sistema',
    'settings'          => 'Configurações',
    'configuration'     => 'Configuração',
    'administration'    => 'Administração',
    'dashboard'         => 'Painel de controle',
    'home'              => 'Início',
    'menu'              => 'Menu',
    'search'            => 'Pesquisar',
    'filter'            => 'Filtrar',
    'sort'              => 'Ordenar',
    
    // Usuário
    'user'              => [
        'profile'       => 'Perfil',
        'settings'      => 'Configurações',
        'logout'        => 'Sair',
        'login'         => 'Entrar',
        'register'      => 'Cadastrar',
        'forgot_password'=> 'Esqueceu a senha?',
        'remember_me'   => 'Lembrar-me',
        'change_password'=> 'Alterar senha',
    ],
    
    // Permissões
    'permissions'       => [
        'title'         => 'Permissões',
        'view'          => 'Visualizar',
        'create'        => 'Criar',
        'edit'          => 'Editar',
        'delete'        => 'Excluir',
        'manage'        => 'Gerenciar',
        'access'        => 'Acessar',
        'denied'        => 'Acesso negado',
    ],
    
    // Navegação
    'navigation'        => [
        'back'          => 'Voltar',
        'next'          => 'Próximo',
        'previous'      => 'Anterior',
        'first'         => 'Primeiro',
        'last'          => 'Último',
        'refresh'       => 'Atualizar',
        'close'         => 'Fechar',
    ],
    
    // Ações
    'actions'           => [
        'save'          => 'Salvar',
        'cancel'        => 'Cancelar',
        'delete'        => 'Excluir',
        'edit'          => 'Editar',
        'update'        => 'Atualizar',
        'create'        => 'Criar',
        'confirm'       => 'Confirmar',
        'apply'         => 'Aplicar',
        'reset'         => 'Redefinir',
        'export'        => 'Exportar',
        'import'        => 'Importar',
    ],
    
    // Mensagens
    'messages'          => [
        'success'       => 'Sucesso',
        'error'         => 'Erro',
        'warning'       => 'Atenção',
        'info'          => 'Informação',
        'confirm'       => 'Confirmar',
        'loading'       => 'Carregando...',
        'processing'    => 'Processando...',
        'saving'        => 'Salvando...',
        'deleting'      => 'Excluindo...',
    ],
    
    // Validação
    'validation'        => [
        'required'      => 'Campo obrigatório',
        'email'         => 'E-mail inválido',
        'min'           => 'Valor mínimo',
        'max'           => 'Valor máximo',
        'numeric'       => 'Deve ser numérico',
        'date'          => 'Data inválida',
        'time'          => 'Hora inválida',
        'unique'        => 'Já existe',
        'confirmed'     => 'Confirmação não corresponde',
    ],
    
    // Datas
    'dates'             => [
        'today'         => 'Hoje',
        'yesterday'     => 'Ontem',
        'tomorrow'      => 'Amanhã',
        'this_week'     => 'Esta semana',
        'last_week'     => 'Semana passada',
        'next_week'     => 'Próxima semana',
        'this_month'    => 'Este mês',
        'last_month'    => 'Mês passado',
        'next_month'    => 'Próximo mês',
        'this_year'     => 'Este ano',
        'last_year'     => 'Ano passado',
        'next_year'     => 'Próximo ano',
    ],
    
    // Tempo
    'time'              => [
        'now'           => 'Agora',
        'morning'       => 'Manhã',
        'afternoon'     => 'Tarde',
        'evening'       => 'Noite',
        'weekend'       => 'Fim de semana',
        'weekday'       => 'Dia útil',
    ],
    
    // Estados
    'states'            => [
        'active'        => 'Ativo',
        'inactive'      => 'Inativo',
        'pending'       => 'Pendente',
        'completed'     => 'Concluído',
        'cancelled'     => 'Cancelado',
        'deleted'       => 'Excluído',
        'draft'         => 'Rascunho',
        'published'     => 'Publicado',
    ],
    
    // Sistema
    'system_info'       => [
        'version'       => 'Versão',
        'environment'   => 'Ambiente',
        'server'        => 'Servidor',
        'php_version'   => 'Versão do PHP',
        'database'      => 'Banco de dados',
        'timezone'      => 'Fuso horário',
        'memory_usage'  => 'Uso de memória',
        'disk_usage'    => 'Uso do disco',
        'select_state' => 'Selecione Estado'
    ],
    
    // Manutenção
    'maintenance'       => [
        'title'         => 'Manutenção',
        'mode'          => 'Modo de manutenção',
        'enable'        => 'Ativar',
        'disable'       => 'Desativar',
        'message'       => 'Mensagem',
        'allowed_ips'   => 'IPs permitidos',
    ],
    
    // Backup
    'backup'            => [
        'title'         => 'Backup',
        'create'        => 'Criar backup',
        'restore'       => 'Restaurar backup',
        'download'      => 'Baixar backup',
        'delete'        => 'Excluir backup',
        'schedule'      => 'Agendar backup',
        'auto'          => 'Backup automático',
    ],
    
    // Logs
    'logs'              => [
        'title'         => 'Logs',
        'view'          => 'Visualizar logs',
        'clear'         => 'Limpar logs',
        'download'      => 'Baixar logs',
        'filter'        => 'Filtrar logs',
        'search'        => 'Pesquisar logs',
    ],
    
    // Cache
    'cache'             => [
        'title'         => 'Cache',
        'clear'         => 'Limpar cache',
        'refresh'       => 'Atualizar cache',
        'status'        => 'Status do cache',
        'size'          => 'Tamanho do cache',
    ],
    
    // Segurança
    'security'          => [
        'title'         => 'Segurança',
        'password'      => 'Senha',
        'two_factor'    => 'Autenticação em dois fatores',
        'session'       => 'Sessão',
        'ip'            => 'IP',
        'last_login'    => 'Último acesso',
        'activity'      => 'Atividade',
    ],
]; 