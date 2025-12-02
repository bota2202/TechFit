# Changelog - TechFit

## [1.0.0] - Melhorias e Correções Completas

### 🔒 Segurança
- ✅ Criado arquivo `config.php` para centralizar credenciais do banco
- ✅ Removido hash duplicado de senha no UsuarioDAO
- ✅ Implementada validação completa de CPF (algoritmo oficial)
- ✅ Melhorado tratamento de erros (não expõe informações sensíveis)
- ✅ Criado sistema de autenticação (`Auth.php`) com proteção de rotas
- ✅ Adicionado `.htaccess` com configurações de segurança

### 🐛 Correções de Bugs
- ✅ Corrigido erro de digitação no SQL: `cidade_usario` → `cidade_usuario`
- ✅ Corrigido formulário de login (adicionado `action` e `method`)
- ✅ Corrigido campo endereço no cadastro (separado `bairro` e `rua`)
- ✅ Corrigido método `update()` no UsuarioDAO (valida senha antes de hash)
- ✅ Removido CSS duplicado em `inicial.css`
- ✅ Criado `dashboard.php` que estava faltando

### 🏗️ Estrutura e Arquitetura
- ✅ Criado `index.php` como router principal
- ✅ Implementado sistema MVC completo e funcional
- ✅ Criada classe `Auth` para gerenciar autenticação
- ✅ Adicionadas constantes para tipos de usuário
- ✅ Melhorada organização de includes e requires

### ✨ Melhorias
- ✅ Validação de CPF completa (frontend e backend)
- ✅ Validação de email duplicado
- ✅ Validação de CPF duplicado
- ✅ Mensagens de erro e sucesso mais amigáveis
- ✅ Sistema de sessão melhorado
- ✅ Logout funcional com mensagem de sucesso
- ✅ Dashboard dinâmico com dados do usuário logado

### 📝 Documentação
- ✅ Criado `README_INSTALACAO.md` com guia completo
- ✅ Criado script SQL de correção (`corrigir_cidade_usuario.sql`)
- ✅ Documentados tipos de usuário no código

### 🔧 Arquivos Criados
- `Código/Model/config.php` - Configurações centralizadas
- `Código/Model/Auth.php` - Sistema de autenticação
- `Código/View/dashboard.php` - Dashboard do aluno
- `index.php` - Router principal
- `.htaccess` - Configurações Apache
- `README_INSTALACAO.md` - Guia de instalação
- `BCD/corrigir_cidade_usuario.sql` - Script de correção SQL

### 🔄 Arquivos Modificados
- `Código/Model/Conexao.php` - Usa config.php
- `Código/Model/UsuarioDAO.php` - Corrigido hash e adicionado readByCPF
- `Código/Controller/UsuarioController.php` - Validações melhoradas
- `Código/View/telalogin.html` - Formulário funcional
- `Código/View/cadastro.html` - Campos corrigidos
- `Código/Public/js/cadastro.js` - Validação CPF completa
- `Código/Public/css/inicial.css` - Removido código duplicado
- `BCD/techfit.sql` - Corrigido nome da coluna
- `BCD/criar_admin.sql` - Corrigido nome da coluna

### ⚠️ Ações Necessárias
1. Execute o script `BCD/corrigir_cidade_usuario.sql` se o banco já existir
2. Ajuste as credenciais em `Código/Model/config.php`
3. Altere a senha do admin após primeiro login
4. Configure o servidor web conforme `README_INSTALACAO.md`

### 📊 Status
- ✅ Todos os problemas críticos corrigidos
- ✅ Sistema funcional e profissional
- ✅ Segurança melhorada
- ✅ Código organizado e documentado

