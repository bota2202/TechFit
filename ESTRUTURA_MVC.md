# Estrutura MVC Completa - TechFit

## 📁 Estrutura de Arquivos

```
TechFit/
├── Código/
│   ├── Controller/          # Controladores (Lógica de Negócio)
│   │   ├── UsuarioController.php
│   │   ├── UnidadeController.php
│   │   ├── PlanoController.php
│   │   ├── CursoController.php
│   │   ├── TurmaController.php
│   │   ├── UsuarioTurmaController.php
│   │   ├── UsuarioPlanoController.php
│   │   └── PagamentoController.php
│   │
│   ├── Model/               # Modelos e Acesso a Dados
│   │   ├── config.php       # Configurações
│   │   ├── Conexao.php      # Cria banco e gerencia conexão
│   │   ├── Auth.php         # Autenticação
│   │   │
│   │   ├── Usuario.php      # Model
│   │   ├── UsuarioDAO.php   # DAO (cria tabela + CRUD)
│   │   │
│   │   ├── Unidade.php
│   │   ├── UnidadeDAO.php   # Cria tabela + insere dados iniciais
│   │   │
│   │   ├── Plano.php
│   │   ├── PlanoDAO.php     # Cria tabela + insere dados iniciais
│   │   │
│   │   ├── Curso.php
│   │   ├── CursoDAO.php     # Cria tabela + insere dados iniciais
│   │   │
│   │   ├── Turma.php
│   │   ├── TurmaDAO.php     # Cria tabela
│   │   │
│   │   ├── UsuarioTurma.php
│   │   ├── UsuarioTurmaDAO.php
│   │   │
│   │   ├── UsuarioPlano.php
│   │   ├── UsuarioPlanoDAO.php
│   │   │
│   │   ├── Pagamento.php
│   │   ├── PagamentoDAO.php
│   │   │
│   │   ├── Presenca.php
│   │   ├── PresencaDAO.php
│   │   │
│   │   ├── Log.php
│   │   └── LogDAO.php
│   │
│   ├── View/                # Views (Interface)
│   │   ├── inicial.html
│   │   ├── telalogin.html
│   │   ├── cadastro.html
│   │   ├── dashboard.php
│   │   ├── cursos.html
│   │   ├── planos.html
│   │   └── unidades.html
│   │
│   └── Public/              # Arquivos Públicos
│       ├── css/
│       ├── js/
│       └── Imagens/
│
└── index.php                 # Router Principal
```

## 🗄️ Tabelas do Banco de Dados

Todas as tabelas são criadas automaticamente pelos DAOs:

1. **Usuarios** - Criada por `UsuarioDAO`
2. **Unidades** - Criada por `UnidadeDAO` (com dados iniciais)
3. **Planos** - Criada por `PlanoDAO` (com dados iniciais)
4. **Cursos** - Criada por `CursoDAO` (com dados iniciais)
5. **Turmas** - Criada por `TurmaDAO`
6. **Usuario_Turma** - Criada por `UsuarioTurmaDAO`
7. **Usuario_Plano** - Criada por `UsuarioPlanoDAO`
8. **Pagamentos** - Criada por `PagamentoDAO`
9. **Presencas** - Criada por `PresencaDAO`
10. **Logs** - Criada por `LogDAO`

## 🔄 Fluxo de Criação

1. **Conexao.php** → Cria o banco de dados `TechFit` se não existir
2. **Cada DAO** → Ao ser instanciado, cria sua tabela automaticamente
3. **DAOs com dados iniciais** → Inserem dados padrão na primeira execução

## 📝 Padrão MVC Implementado

### Model (Modelo)
- **Classe Model**: Representa a entidade (ex: `Usuario.php`)
- **Classe DAO**: Acesso a dados + criação de tabela (ex: `UsuarioDAO.php`)

### View (Visualização)
- Arquivos HTML/PHP que exibem a interface

### Controller (Controlador)
- Processa requisições, valida dados, chama DAOs

## 🛣️ Rotas Disponíveis

- `index.php?action=store` - Cadastro de usuário
- `index.php?action=login` - Login
- `index.php?action=logout` - Logout
- `index.php?action=unidade-cadastrar` - Cadastrar unidade
- `index.php?action=turma-cadastrar` - Cadastrar turma
- `index.php?action=matricular` - Matricular em turma
- `index.php?action=contratar-plano` - Contratar plano
- `index.php?action=registrar-pagamento` - Registrar pagamento

## ✅ Características

- ✅ Banco criado automaticamente
- ✅ Tabelas criadas automaticamente
- ✅ Dados iniciais inseridos automaticamente
- ✅ Estrutura MVC completa para cada tabela
- ✅ Validações e tratamento de erros
- ✅ Sistema de autenticação
- ✅ Logs de ações

