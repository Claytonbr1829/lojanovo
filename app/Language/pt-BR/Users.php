<?php

return [
    // Geral
    'users'             => 'Usuários',
    'user'              => 'Usuário',
    'new_user'          => 'Novo usuário',
    'edit_user'         => 'Editar usuário',
    'delete_user'       => 'Excluir usuário',
    'user_details'      => 'Detalhes do usuário',
    'user_profile'      => 'Perfil do usuário',
    'user_settings'     => 'Configurações do usuário',
    
    // Campos
    'fields' => [
        'name'          => 'Nome',
        'idempresa'     => 'Id Empresa',
        'email'         => 'E-mail',
        'phone'         => 'Telefone',
        'password'      => 'Senha',
        'confirm_password'=> 'Confirmar senha',
        'current_password'=> 'Senha atual',
        'new_password'  => 'Nova senha',
        'avatar'        => 'Avatar',
        'role'          => 'Função',
        'status'        => 'Status',
        'last_login'    => 'Último acesso',
        'created_at'    => 'Criado em',
        'updated_at'    => 'Atualizado em',
    ],
    // Status
    'status'            => [
        'active'        => 'Ativo',
        'inactive'      => 'Inativo',
        'pending'       => 'Pendente',
        'blocked'       => 'Bloqueado',
        'deleted'       => 'Excluído',
    ],
    
    // Funções
    'roles'             => [
        'title'         => 'Funções',
        'create'        => 'Criar função',
        'edit'          => 'Editar função',
        'delete'        => 'Excluir função',
        'name'          => 'Nome',
        'description'   => 'Descrição',
        'permissions'   => 'Permissões',
        'admin'         => 'Administrador',
        'manager'       => 'Gerente',
        'editor'        => 'Editor',
        'user'          => 'Usuário',
        'customer'      => 'Cliente',
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
        'users'         => 'Usuários',
        'products'      => 'Produtos',
        'orders'        => 'Pedidos',
        'categories'    => 'Categorias',
        'settings'      => 'Configurações',
    ],
    
    // Autenticação
    'auth'              => [
        'title'         => 'Autenticação',
        'login'         => 'Entrar',
        'logout'        => 'Sair',
        'register'      => 'Cadastrar',
        'forgot_password'=> 'Esqueceu a senha?',
        'reset_password'=> 'Redefinir senha',
        'remember_me'   => 'Lembrar-me',
        'verify_email'  => 'Verificar e-mail',
        'resend_verification'=> 'Reenviar verificação',
        'two_factor'    => 'Autenticação em dois fatores',
        'recovery_codes'=> 'Códigos de recuperação',
    ],
    
    // Perfil
    'profile'           => [
        'title'         => 'Perfil',
        'edit'          => 'Editar perfil',
        'update'        => 'Atualizar perfil',
        'avatar'        => 'Avatar',
        'personal_info' => 'Informações pessoais',
        'contact_info'  => 'Informações de contato',
        'security'      => 'Segurança',
        'preferences'   => 'Preferências',
        'notifications' => 'Notificações',
    ],
    
    // Endereços
    'addresses'         => [
        'title'         => 'Endereços',
        'add'           => 'Adicionar endereço',
        'edit'          => 'Editar endereço',
        'delete'        => 'Excluir endereço',
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
    
    // Pedidos
    'orders'            => [
        'title'         => 'Pedidos',
        'view'          => 'Visualizar pedido',
        'history'       => 'Histórico de pedidos',
        'tracking'      => 'Rastreamento',
        'invoice'       => 'Fatura',
        'status'        => 'Status',
        'date'          => 'Data',
        'total'         => 'Total',
        'items'         => 'Itens',
    ],
    
    // Lista de desejos
    'wishlist'          => [
        'title'         => 'Lista de desejos',
        'add'           => 'Adicionar à lista',
        'remove'        => 'Remover da lista',
        'move_to_cart'  => 'Mover para o carrinho',
        'items'         => 'Itens',
        'empty'         => 'Lista vazia',
    ],
    
    // Mensagens
    'messages'          => [
        'created'       => 'Usuário criado com sucesso',
        'updated'       => 'Usuário atualizado com sucesso',
        'deleted'       => 'Usuário excluído com sucesso',
        'activated'     => 'Usuário ativado com sucesso',
        'deactivated'   => 'Usuário desativado com sucesso',
        'blocked'       => 'Usuário bloqueado com sucesso',
        'unblocked'     => 'Usuário desbloqueado com sucesso',
        'password_changed'=> 'Senha alterada com sucesso',
        'profile_updated'=> 'Perfil atualizado com sucesso',
    ],
    
    // Erros
    'errors'            => [
        'create_failed' => 'Erro ao criar usuário',
        'update_failed' => 'Erro ao atualizar usuário',
        'delete_failed' => 'Erro ao excluir usuário',
        'not_found'     => 'Usuário não encontrado',
        'invalid_credentials'=> 'Credenciais inválidas',
        'email_not_verified'=> 'E-mail não verificado',
        'account_blocked'=> 'Conta bloqueada',
        'password_mismatch'=> 'Senhas não conferem',
        'email_exists'  => 'E-mail já cadastrado',
    ],
    
    // Validação
    'validation'        => [
        'required'      => 'Campo obrigatório',
        'email'         => 'E-mail inválido',
        'min'           => 'Valor mínimo',
        'max'           => 'Valor máximo',
        'numeric'       => 'Deve ser numérico',
        'unique'        => 'Já existe',
        'confirmed'     => 'Confirmação não corresponde',
        'password'      => 'Senha inválida',
        'phone'         => 'Telefone inválido',
        'zip_code'      => 'CEP inválido',
    ],
    
    // Notificações
    'notifications'     => [
        'title'         => 'Notificações',
        'mark_read'     => 'Marcar como lida',
        'mark_all_read' => 'Marcar todas como lidas',
        'clear_all'     => 'Limpar todas',
        'settings'      => 'Configurações',
        'email'         => 'E-mail',
        'sms'           => 'SMS',
        'push'          => 'Push',
    ],
]; 