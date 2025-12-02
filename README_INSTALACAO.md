# TechFit - Guia de Instalação

## Requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache com mod_rewrite habilitado
- Extensões PHP: PDO, PDO_MySQL, mbstring

## Instalação

### 1. Configurar Banco de Dados

Execute os scripts SQL na seguinte ordem:

```bash
# 1. Criar o banco de dados e tabelas
mysql -u root -p < BCD/techfit.sql

# 2. Corrigir erro de digitação (se necessário)
mysql -u root -p < BCD/corrigir_cidade_usuario.sql

# 3. Criar usuário administrador
mysql -u root -p < BCD/criar_admin.sql
```

### 2. Configurar Credenciais

Edite o arquivo `Código/Model/config.php` e ajuste as credenciais do banco de dados:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'academia_techfit');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

**IMPORTANTE**: Em produção, use variáveis de ambiente ou um arquivo de configuração fora do versionamento.

### 3. Configurar Servidor Web

#### Apache
Certifique-se de que o `.htaccess` está funcionando e o `mod_rewrite` está habilitado.

#### Nginx
Adicione as seguintes configurações:

```nginx
location / {
    try_files $uri $uri/ /index.php?action=$uri&$args;
}
```

### 4. Permissões

Certifique-se de que o servidor web tem permissão de leitura em todos os arquivos:

```bash
chmod -R 755 Código/
chmod 644 .htaccess
```

### 5. Acessar o Sistema

Acesse: `http://localhost/TechFit/`

## Credenciais Padrão do Admin

Após executar `criar_admin.sql`:
- **Email**: admin@techfit.com
- **Senha**: admin123

**IMPORTANTE**: Altere a senha após o primeiro login!

## Estrutura do Projeto

```
TechFit/
├── Código/
│   ├── Controller/        # Controladores (lógica de negócio)
│   ├── Model/            # Modelos (acesso a dados)
│   ├── View/             # Views (interface)
│   └── Public/           # Arquivos públicos (CSS, JS, imagens)
├── BCD/                  # Scripts SQL
└── index.php             # Router principal
```

## Tipos de Usuário

- **1**: Administrador
- **2**: Instrutor
- **3**: Aluno (padrão)

## Troubleshooting

### Erro de conexão com banco
- Verifique as credenciais em `config.php`
- Certifique-se de que o MySQL está rodando
- Verifique se o banco `academia_techfit` foi criado

### Página em branco
- Verifique os logs de erro do PHP
- Certifique-se de que todas as extensões PHP necessárias estão instaladas
- Verifique permissões de arquivo

### Erro 404 nas rotas
- Certifique-se de que o `mod_rewrite` está habilitado (Apache)
- Verifique a configuração do `.htaccess`
- Para Nginx, configure o rewrite conforme mostrado acima

## Segurança

- **NUNCA** versione o arquivo `config.php` com credenciais reais
- Use senhas fortes em produção
- Mantenha o PHP e MySQL atualizados
- Configure HTTPS em produção
- Faça backups regulares do banco de dados

