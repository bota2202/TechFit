# TechFit - Sistema Completo

## 📋 Visão Geral

Este projeto foi completado com um backend PHP completo e um painel administrativo. O sistema permite gerenciar usuários, planos, cursos, turmas, unidades, pagamentos e presenças.

## 🔐 Acesso Administrativo

O painel administrativo está disponível **APENAS** para o usuário com `id_usuario = 1`.

### Credenciais Padrão do Admin:
- **Email:** admin@techfit.com
- **Senha:** admin123

⚠️ **IMPORTANTE:** Altere a senha após o primeiro login!

## 🚀 Instalação

### 1. Banco de Dados

Execute o script SQL em ordem:

```sql
-- 1. Criar o banco e tabelas
source BCD/techfit.sql

-- 2. Criar o usuário admin
source BCD/criar_admin.sql
```

### 2. Configuração

Edite o arquivo `Código/DB/conexao.php` com suas credenciais do MySQL:

```php
$server="localhost";
$user="root";
$db="techfit";
$senha="sua_senha_aqui";
```

### 3. Servidor Web

Coloque a pasta `Código` no diretório do seu servidor web (Apache/Nginx com PHP).

## 📁 Estrutura do Backend

### APIs Disponíveis

Todas as APIs estão em `Código/DB/`:

- **api_usuarios.php** - Gerenciar usuários (apenas admin)
- **api_planos.php** - Gerenciar planos (GET público, resto admin)
- **api_cursos.php** - Gerenciar cursos (GET público, resto admin)
- **api_turmas.php** - Gerenciar turmas (apenas admin)
- **api_unidades.php** - Gerenciar unidades (GET público, resto admin)
- **api_pagamentos.php** - Gerenciar pagamentos
- **api_presencas.php** - Gerenciar presenças
- **api_matriculas.php** - Matricular/desmatricular em turmas
- **api_planos_usuario.php** - Associar planos a usuários

### Autenticação

- **login.php** - Fazer login
- **logout.php** - Fazer logout
- **verificar_sessao.php** - Verificar sessão e permissões

### Cadastro

- **cadastro.php** - Cadastrar novos usuários

## 🎛️ Painel Administrativo

Acesse em: `Código/PageAdmin/admin.php`

O painel permite:
- ✅ Visualizar dashboard com estatísticas
- ✅ Gerenciar usuários (criar, editar, deletar)
- ✅ Gerenciar planos
- ✅ Gerenciar cursos
- ✅ Gerenciar turmas
- ✅ Gerenciar unidades
- ✅ Gerenciar pagamentos
- ✅ Gerenciar presenças

## 🔒 Segurança

- Todas as senhas são hasheadas com `password_hash()`
- Sessões PHP são usadas para autenticação
- Verificação de permissões em todas as APIs
- Apenas usuário com `id = 1` pode acessar o painel admin
- Proteção contra SQL Injection usando prepared statements

## 📝 Funcionalidades Implementadas

### Frontend Integrado
- ✅ Login funcional com redirecionamento (admin → painel, usuário → área do aluno)
- ✅ Cadastro de usuários integrado com backend
- ✅ Planos carregados do banco de dados
- ✅ Cursos carregados do banco de dados
- ✅ Unidades carregadas do banco de dados
- ✅ Turmas exibidas nos cursos

### Backend Completo
- ✅ Sistema de autenticação e sessão
- ✅ CRUD completo para todas as entidades
- ✅ APIs RESTful
- ✅ Logs de ações dos usuários
- ✅ Validações de dados

## 🎨 Estilo Visual

O painel administrativo segue o mesmo estilo visual do site:
- Gradientes verdes (#11998e → #38ef7d)
- Design moderno e responsivo
- Animações suaves
- Cards com glassmorphism

## 📊 Banco de Dados

O banco de dados inclui as seguintes tabelas:
- `Usuarios` - Usuários do sistema
- `Planos` - Planos disponíveis
- `Cursos` - Cursos oferecidos
- `Turmas` - Turmas dos cursos
- `Unidades` - Unidades físicas
- `Usuario_Turma` - Matrículas
- `Usuario_Plano` - Planos dos usuários
- `Pagamentos` - Histórico de pagamentos
- `Presencas` - Controle de presença
- `Logs` - Log de ações

## 🛠️ Próximos Passos (Opcional)

Funcionalidades que podem ser expandidas:
- Modais completos para criar/editar no painel admin
- Área do aluno com dados reais do banco
- Sistema de notificações
- Relatórios e gráficos
- Exportação de dados
- Upload de imagens

## ⚠️ Notas Importantes

1. O usuário admin deve ter `id_usuario = 1` e `tipo_usuario = 1`
2. A senha padrão do admin é "admin123" - altere após o primeiro login
3. Certifique-se de que o PHP está configurado com extensão mysqli
4. As sessões PHP devem estar habilitadas

## 📞 Suporte

Em caso de problemas, verifique:
- Logs do PHP
- Logs do MySQL
- Console do navegador (F12)
- Permissões de arquivo no servidor

