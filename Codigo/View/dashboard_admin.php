<?php

session_start();

require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';
require_once __DIR__ . '/../Model/Conexao.php';
require_once __DIR__ . '/../Model/UsuarioDAO.php';
require_once __DIR__ . '/../Model/TreinoDAO.php';
require_once __DIR__ . '/../Model/DietaDAO.php';
require_once __DIR__ . '/../Model/TurmaDAO.php';
require_once __DIR__ . '/../Model/CursoDAO.php';
require_once __DIR__ . '/../Model/UnidadeDAO.php';
require_once __DIR__ . '/../Model/PlanoDAO.php';
require_once __DIR__ . '/../Model/UsuarioTurmaDAO.php';
require_once __DIR__ . '/../Model/AvaliacaoFisicaDAO.php';
require_once __DIR__ . '/../Model/MensagemDAO.php';
require_once __DIR__ . '/../Model/helpers.php';

Auth::requireAdmin();

$usuario = $_SESSION['usuario'];
$usuarioDAO = new UsuarioDAO();
$treinoDAO = new TreinoDAO();
$dietaDAO = new DietaDAO();
$turmaDAO = new TurmaDAO();
$cursoDAO = new CursoDAO();
$unidadeDAO = new UnidadeDAO();
$planoDAO = new PlanoDAO();
$usuarioTurmaDAO = new UsuarioTurmaDAO();
$avaliacaoDAO = new AvaliacaoFisicaDAO();
$mensagemDAO = new MensagemDAO();

$usuarios = $usuarioDAO->readAll();
$alunos = [];
$instrutores = [];
foreach ($usuarios as $u) {
    if ($u->getTipo() == TIPO_USUARIO_ALUNO) {
        $alunos[] = $u;
    } elseif ($u->getTipo() == TIPO_USUARIO_INSTRUTOR) {
        $instrutores[] = $u;
    }
}

$unidades = $unidadeDAO->readAll();
$cursos = $cursoDAO->readAll();
$planos = $planoDAO->readAll();
$turmas = $turmaDAO->readAll();

// Estatísticas
$totalAlunos = count($alunos);
$totalTurmas = count($turmas);
$totalCursos = count($cursos);
$totalUnidades = count($unidades);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Dashboard Admin</title>
    <link rel="icon" type="image/svg+xml" href="../Public/favicon.svg">
    <link rel="alternate icon" href="../Public/favicon.svg">
    <link rel="stylesheet" href="../Public/css/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Define função global IMEDIATAMENTE no head para estar disponível quando o HTML for renderizado
        window.abrirModalEditarUsuario = function(btn, e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            console.log('Botão de editar clicado!', btn);
            
            const id = btn.getAttribute('data-id');
            const nome = btn.getAttribute('data-nome') || '';
            const email = btn.getAttribute('data-email') || '';
            const cpf = btn.getAttribute('data-cpf') || '';
            const telefone = btn.getAttribute('data-telefone') || '';
            const tipo = btn.getAttribute('data-tipo') || '3';
            const estado = btn.getAttribute('data-estado') || '';
            const cidade = btn.getAttribute('data-cidade') || '';
            const bairro = btn.getAttribute('data-bairro') || '';
            const rua = btn.getAttribute('data-rua') || '';
            
            console.log('Dados:', {id, nome, email});
            
            const usuarioId = document.getElementById('usuario_id');
            const usuarioNome = document.getElementById('usuario_nome');
            const usuarioEmail = document.getElementById('usuario_email');
            const usuarioCpf = document.getElementById('usuario_cpf');
            const usuarioTelefone = document.getElementById('usuario_telefone');
            const usuarioTipo = document.getElementById('usuario_tipo');
            const usuarioEstado = document.getElementById('usuario_estado');
            const usuarioCidade = document.getElementById('usuario_cidade');
            const usuarioBairro = document.getElementById('usuario_bairro');
            const usuarioRua = document.getElementById('usuario_rua');
            const usuarioSenha = document.getElementById('usuario_senha');
            
            if (usuarioId) usuarioId.value = id || '';
            if (usuarioNome) usuarioNome.value = nome;
            if (usuarioEmail) usuarioEmail.value = email;
            if (usuarioCpf) usuarioCpf.value = cpf;
            if (usuarioTelefone) usuarioTelefone.value = telefone;
            if (usuarioTipo) usuarioTipo.value = tipo;
            if (usuarioEstado) usuarioEstado.value = estado;
            if (usuarioCidade) usuarioCidade.value = cidade;
            if (usuarioBairro) usuarioBairro.value = bairro;
            if (usuarioRua) usuarioRua.value = rua;
            if (usuarioSenha) usuarioSenha.value = '';
            
            const modalElement = document.getElementById('modalEditarUsuario');
            if (!modalElement) {
                console.error('Modal não encontrado!');
                alert('Erro: Modal não encontrado');
                return;
            }
            
            console.log('Modal encontrado, tentando abrir...');
            
            // Tenta usar Bootstrap primeiro
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try {
                    let modal = bootstrap.Modal.getInstance(modalElement);
                    if (!modal) {
                        console.log('Criando nova instância do modal Bootstrap');
                        modal = new bootstrap.Modal(modalElement, {
                            backdrop: true,
                            keyboard: true
                        });
                    }
                    console.log('Mostrando modal Bootstrap');
                    modal.show();
                    return;
                } catch (error) {
                    console.error('Erro ao usar Bootstrap Modal:', error);
                }
            } else {
                console.warn('Bootstrap não está disponível, usando fallback');
            }
            
            // Fallback manual
            console.log('Usando fallback manual para mostrar modal');
            
            // Remove backdrop existente se houver
            const existingBackdrop = document.getElementById('modal-backdrop-editar');
            if (existingBackdrop) {
                existingBackdrop.remove();
            }
            
            // Cria backdrop primeiro
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'modal-backdrop-editar';
            backdrop.style.position = 'fixed';
            backdrop.style.top = '0';
            backdrop.style.left = '0';
            backdrop.style.width = '100%';
            backdrop.style.height = '100%';
            backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            backdrop.style.zIndex = '1040';
            backdrop.addEventListener('click', function() {
                window.fecharModalEditarUsuario();
            });
            document.body.appendChild(backdrop);
            
            // Agora mostra o modal
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            modalElement.setAttribute('aria-hidden', 'false');
            modalElement.setAttribute('aria-modal', 'true');
            modalElement.style.zIndex = '1050';
            modalElement.style.position = 'fixed';
            modalElement.style.top = '0';
            modalElement.style.left = '0';
            modalElement.style.width = '100%';
            modalElement.style.height = '100%';
            modalElement.style.display = 'flex';
            modalElement.style.alignItems = 'center';
            modalElement.style.justifyContent = 'center';
            
            const modalDialog = modalElement.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.position = 'relative';
                modalDialog.style.zIndex = '1051';
            }
            
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';
            document.body.style.paddingRight = '0px';
            
            console.log('Modal deve estar visível agora');
        };
        
        window.fecharModalEditarUsuario = function() {
            const modalElement = document.getElementById('modalEditarUsuario');
            if (modalElement) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                        return;
                    }
                }
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.setAttribute('aria-modal', 'false');
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            const backdrop = document.getElementById('modal-backdrop-editar');
            if (backdrop) {
                backdrop.remove();
            }
        };
    </script>
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px;
        }
        .admin-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 20px;
            margin-bottom: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }
        .stat-card {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #11998e;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        .nav-tabs .nav-link {
            color: #666;
            border: none;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: #11998e;
            border-bottom: 2px solid #11998e;
            background: transparent;
        }
        .tab-content {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table-actions {
            white-space: nowrap;
        }
        .table-actions .btn {
            margin-right: 5px;
        }
        .table-actions .btn:last-child {
            margin-right: 0;
        }
        .modal-lg {
            max-width: 800px;
        }
        .modal-body .row {
            margin-bottom: 0;
        }
        .modal-body .mb-3 {
            margin-bottom: 1rem !important;
        }
    </style>
</head>
<body>
    <nav>
        <section class="nav-esquerda">
            <p class="logo_techfit">TechFit</p>
        </section>

        <section class="nav-centro">
            <a class="btn-nav-centro" href="inicial.php#hero">Início</a>
            <a class="btn-nav-centro" href="planos.php">Planos</a>
            <a class="btn-nav-centro" href="unidades.php">Unidades</a>
            <a class="btn-nav-centro" href="cursos.php">Cursos</a>
            <a class="btn-nav-centro btn-ativo" href="dashboard_admin.php">Dashboard Admin</a>
        </section>

        <section class="nav-direita">
            <div class="usuario-menu">
                <div class="usuario-avatar">
                    <?php echo strtoupper(substr($usuario['nome'], 0, 1)); ?>
                </div>
                <div class="usuario-dropdown">
                    <a href="#" class="usuario-dropdown-item">
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($usuario['nome']); ?>
                    </a>
                    <a href="mensagens.php" class="usuario-dropdown-item">
                        <i class="fas fa-envelope me-2"></i>Mensagens
                    </a>
                    <a href="perfil.php" class="usuario-dropdown-item">
                        <i class="fas fa-cog me-2"></i>Configurações
                    </a>
                    <a href="<?php echo getActionUrl('logout'); ?>" class="usuario-dropdown-item logout">
                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                    </a>
                </div>
            </div>
        </section>
    </nav>

    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-user-shield me-2"></i>Dashboard Admin</h1>
            </div>
        </div>
    </div>

    <div class="container">
        <?php
        if (isset($_SESSION['erro'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['erro']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['erro']);
        }
        if (isset($_SESSION['sucesso'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['sucesso']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['sucesso']);
        }
        ?>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalAlunos; ?></div>
                    <div class="stat-label">Total de Alunos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalTurmas; ?></div>
                    <div class="stat-label">Turmas Ativas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalCursos; ?></div>
                    <div class="stat-label">Cursos Disponíveis</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalUnidades; ?></div>
                    <div class="stat-label">Unidades</div>
                </div>
            </div>
        </div>

        <!-- Abas de Gestão -->
        <ul class="nav nav-tabs mb-3" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="unidades-tab" data-bs-toggle="tab" data-bs-target="#unidades" type="button" role="tab">
                    <i class="fas fa-building me-2"></i>Unidades
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cursos-tab" data-bs-toggle="tab" data-bs-target="#cursos" type="button" role="tab">
                    <i class="fas fa-book me-2"></i>Cursos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="planos-tab" data-bs-toggle="tab" data-bs-target="#planos" type="button" role="tab">
                    <i class="fas fa-credit-card me-2"></i>Planos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="turmas-tab" data-bs-toggle="tab" data-bs-target="#turmas" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>Turmas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>Usuários
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="treinos-tab" data-bs-toggle="tab" data-bs-target="#treinos" type="button" role="tab">
                    <i class="fas fa-dumbbell me-2"></i>Treinos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dietas-tab" data-bs-toggle="tab" data-bs-target="#dietas" type="button" role="tab">
                    <i class="fas fa-utensils me-2"></i>Dietas
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabContent">
            <!-- Aba Unidades -->
            <div class="tab-pane fade show active" id="unidades" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Unidades</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalUnidade">
                        <i class="fas fa-plus me-2"></i>Nova Unidade
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Bairro</th>
                                <th>Rua</th>
                                <th>Número</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unidades as $unidade): ?>
                                <tr>
                                    <td><?php echo $unidade->getId(); ?></td>
                                    <td><?php echo htmlspecialchars($unidade->getEstado()); ?></td>
                                    <td><?php echo htmlspecialchars($unidade->getCidade()); ?></td>
                                    <td><?php echo htmlspecialchars($unidade->getBairro()); ?></td>
                                    <td><?php echo htmlspecialchars($unidade->getRua()); ?></td>
                                    <td><?php echo $unidade->getNumero(); ?></td>
                                    <td class="table-actions">
                                        <button class="btn btn-sm btn-warning" onclick="editarUnidade(<?php echo $unidade->getId(); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?php echo getActionUrl('unidade-deletar'); ?>" method="POST" style="display:inline;" class="form-deletar" data-tipo="unidade" data-nome="<?php echo htmlspecialchars($unidade->getBairro()); ?>">
                                            <input type="hidden" name="id" value="<?php echo $unidade->getId(); ?>">
                                            <button type="button" class="btn btn-sm btn-danger btn-deletar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Cursos -->
            <div class="tab-pane fade" id="cursos" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Cursos</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalCurso">
                        <i class="fas fa-plus me-2"></i>Novo Curso
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Preço</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $curso): ?>
                                <tr>
                                    <td><?php echo $curso->getId(); ?></td>
                                    <td><?php echo htmlspecialchars($curso->getNome()); ?></td>
                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($curso->getTipo()); ?></span></td>
                                    <td><?php echo htmlspecialchars(substr($curso->getDescricao(), 0, 50)); ?>...</td>
                                    <td>R$ <?php echo number_format($curso->getPreco(), 2, ',', '.'); ?></td>
                                    <td class="table-actions">
                                        <button class="btn btn-sm btn-warning" onclick="editarCurso(<?php echo $curso->getId(); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?php echo getActionUrl('curso-deletar'); ?>" method="POST" style="display:inline;" class="form-deletar" data-tipo="curso" data-nome="<?php echo htmlspecialchars($curso->getNome()); ?>">
                                            <input type="hidden" name="id" value="<?php echo $curso->getId(); ?>">
                                            <button type="button" class="btn btn-sm btn-danger btn-deletar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Planos -->
            <div class="tab-pane fade" id="planos" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Planos</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalPlano">
                        <i class="fas fa-plus me-2"></i>Novo Plano
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Preço</th>
                                <th>Descrição</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($planos as $plano): ?>
                                <tr>
                                    <td><?php echo $plano->getId(); ?></td>
                                    <td>R$ <?php echo number_format($plano->getPreco(), 2, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($plano->getDescricao()); ?></td>
                                    <td class="table-actions">
                                        <button class="btn btn-sm btn-warning" onclick="editarPlano(<?php echo $plano->getId(); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?php echo getActionUrl('plano-deletar'); ?>" method="POST" style="display:inline;" class="form-deletar" data-tipo="plano" data-nome="<?php echo htmlspecialchars($plano->getDescricao()); ?>">
                                            <input type="hidden" name="id" value="<?php echo $plano->getId(); ?>">
                                            <button type="button" class="btn btn-sm btn-danger btn-deletar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Turmas -->
            <div class="tab-pane fade" id="turmas" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Turmas</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTurma">
                        <i class="fas fa-plus me-2"></i>Nova Turma
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Curso</th>
                                <th>Horário</th>
                                <th>Data Início</th>
                                <th>Data Fim</th>
                                <th>Capacidade</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($turmas as $turma): ?>
                                <?php 
                                $curso = $cursoDAO->readById($turma->getIdCurso());
                                $matriculados = $usuarioTurmaDAO->readByTurma($turma->getId());
                                ?>
                                <tr>
                                    <td><?php echo $turma->getId(); ?></td>
                                    <td><?php echo htmlspecialchars($turma->getNome()); ?></td>
                                    <td><?php echo $curso ? htmlspecialchars($curso->getNome()) : 'N/A'; ?></td>
                                    <td><?php echo htmlspecialchars($turma->getHorario()); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($turma->getDataInicio())); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($turma->getDataFim())); ?></td>
                                    <td><?php echo count($matriculados); ?>/<?php echo $turma->getCapacidadeMaxima() ?: 20; ?></td>
                                    <td class="table-actions">
                                        <button class="btn btn-sm btn-info" onclick="verTurma(<?php echo $turma->getId(); ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editarTurma(<?php echo $turma->getId(); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?php echo getActionUrl('turma-deletar'); ?>" method="POST" style="display:inline;" class="form-deletar" data-tipo="turma" data-nome="<?php echo htmlspecialchars($turma->getNome()); ?>">
                                            <input type="hidden" name="id" value="<?php echo $turma->getId(); ?>">
                                            <button type="button" class="btn btn-sm btn-danger btn-deletar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Usuários -->
            <div class="tab-pane fade" id="usuarios" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Usuários</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>CPF</th>
                                <th>Tipo</th>
                                <th>Cidade</th>
                                <th>Estado</th>
                                <th>Telefone</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $user): ?>
                                <tr>
                                    <td><?php echo $user->getId(); ?></td>
                                    <td><?php echo htmlspecialchars($user->getNome()); ?></td>
                                    <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                                    <td><?php echo htmlspecialchars($user->getCpf()); ?></td>
                                    <td>
                                        <?php 
                                        $tipo = $user->getTipo();
                                        $tipoNome = '';
                                        $tipoBadge = '';
                                        if ($tipo == TIPO_USUARIO_ADMIN) {
                                            $tipoNome = 'Admin';
                                            $tipoBadge = 'danger';
                                        } elseif ($tipo == TIPO_USUARIO_INSTRUTOR) {
                                            $tipoNome = 'Instrutor';
                                            $tipoBadge = 'warning';
                                        } else {
                                            $tipoNome = 'Aluno';
                                            $tipoBadge = 'info';
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $tipoBadge; ?>"><?php echo $tipoNome; ?> (<?php echo $tipo; ?>)</span>
                                    </td>
                                    <td><?php echo htmlspecialchars($user->getCidade() ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($user->getEstado() ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($user->getTelefone() ?? ''); ?></td>
                                    <td class="table-actions">
                                        <button type="button" class="btn btn-sm btn-warning btn-editar-usuario" 
                                                data-id="<?php echo $user->getId(); ?>"
                                                data-nome="<?php echo htmlspecialchars($user->getNome(), ENT_QUOTES); ?>"
                                                data-email="<?php echo htmlspecialchars($user->getEmail(), ENT_QUOTES); ?>"
                                                data-cpf="<?php echo htmlspecialchars($user->getCpf(), ENT_QUOTES); ?>"
                                                data-telefone="<?php echo htmlspecialchars($user->getTelefone() ?? '', ENT_QUOTES); ?>"
                                                data-tipo="<?php echo $user->getTipo(); ?>"
                                                data-estado="<?php echo htmlspecialchars($user->getEstado() ?? '', ENT_QUOTES); ?>"
                                                data-cidade="<?php echo htmlspecialchars($user->getCidade() ?? '', ENT_QUOTES); ?>"
                                                data-bairro="<?php echo htmlspecialchars($user->getBairro() ?? '', ENT_QUOTES); ?>"
                                                data-rua="<?php echo htmlspecialchars($user->getRua() ?? '', ENT_QUOTES); ?>"
                                                onclick="abrirModalEditarUsuario(this, event)">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <?php if ($user->getTipo() == TIPO_USUARIO_ALUNO): ?>
                                        <a href="avaliacoes.php?usuario=<?php echo $user->getId(); ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-clipboard-list"></i> Avaliações
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Treinos -->
            <div class="tab-pane fade" id="treinos" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Treinos</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTreino">
                        <i class="fas fa-plus me-2"></i>Novo Treino
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Aluno</th>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Data</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $todosTreinos = [];
                            foreach ($alunos as $aluno) {
                                $treinosAluno = $treinoDAO->readByUsuarioId($aluno->getId());
                                foreach ($treinosAluno as $treino) {
                                    $todosTreinos[] = ['treino' => $treino, 'aluno' => $aluno];
                                }
                            }
                            ?>
                            <?php if (!empty($todosTreinos)): ?>
                                <?php foreach ($todosTreinos as $item): ?>
                                    <tr>
                                        <td><?php echo $item['treino']['id']; ?></td>
                                        <td><?php echo htmlspecialchars($item['aluno']->getNome()); ?></td>
                                        <td><?php echo htmlspecialchars($item['treino']['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($item['treino']['descricao'], 0, 50)); ?>...</td>
                                        <td><?php echo date('d/m/Y', strtotime($item['treino']['data_criacao'])); ?></td>
                                        <td class="table-actions">
                                            <form action="<?php echo getActionUrl('treino-deletar'); ?>" method="POST" style="display:inline;" class="form-deletar" data-tipo="treino" data-nome="<?php echo htmlspecialchars($treino->getNome()); ?>">
                                                <input type="hidden" name="id" value="<?php echo $item['treino']['id']; ?>">
                                                <button type="button" class="btn btn-sm btn-danger btn-deletar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Nenhum treino cadastrado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Dietas -->
            <div class="tab-pane fade" id="dietas" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Dietas</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalDieta">
                        <i class="fas fa-plus me-2"></i>Nova Dieta
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Aluno</th>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Data</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $todasDietas = [];
                            foreach ($alunos as $aluno) {
                                $dietasAluno = $dietaDAO->readByUsuarioId($aluno->getId());
                                foreach ($dietasAluno as $dieta) {
                                    $todasDietas[] = ['dieta' => $dieta, 'aluno' => $aluno];
                                }
                            }
                            ?>
                            <?php if (!empty($todasDietas)): ?>
                                <?php foreach ($todasDietas as $item): ?>
                                    <tr>
                                        <td><?php echo $item['dieta']['id']; ?></td>
                                        <td><?php echo htmlspecialchars($item['aluno']->getNome()); ?></td>
                                        <td><?php echo htmlspecialchars($item['dieta']['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($item['dieta']['descricao'], 0, 50)); ?>...</td>
                                        <td><?php echo date('d/m/Y', strtotime($item['dieta']['data_criacao'])); ?></td>
                                        <td class="table-actions">
                                            <form action="<?php echo getActionUrl('dieta-deletar'); ?>" method="POST" style="display:inline;" class="form-deletar" data-tipo="dieta" data-nome="<?php echo htmlspecialchars($dieta->getNome()); ?>">
                                                <input type="hidden" name="id" value="<?php echo $item['dieta']['id']; ?>">
                                                <button type="button" class="btn btn-sm btn-danger btn-deletar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Nenhuma dieta cadastrada</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Unidade -->
    <div class="modal fade" id="modalUnidade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Unidade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo getActionUrl('unidade-cadastrar'); ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <input type="text" name="estado" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="cidade" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bairro</label>
                            <input type="text" name="bairro" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rua</label>
                            <input type="text" name="rua" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Número</label>
                            <input type="number" name="numero" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Curso -->
    <div class="modal fade" id="modalCurso" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo getActionUrl('curso-cadastrar'); ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="forca">Força</option>
                                <option value="cardio">Cardio</option>
                                <option value="mente-corpo">Mente-Corpo</option>
                                <option value="lutas">Lutas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preço</label>
                            <input type="number" step="0.01" name="preco" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Plano -->
    <div class="modal fade" id="modalPlano" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Plano</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo getActionUrl('plano-cadastrar'); ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Preço</label>
                            <input type="number" step="0.01" name="preco" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Turma -->
    <div class="modal fade" id="modalTurma" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Turma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo getActionUrl('turma-cadastrar'); ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Curso</label>
                            <select name="id_curso" class="form-select" required>
                                <option value="">Selecione um curso</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?php echo $curso->getId(); ?>">
                                        <?php echo htmlspecialchars($curso->getNome()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Responsável</label>
                            <select name="responsavel" class="form-select" required>
                                <option value="">Selecione um instrutor</option>
                                <?php foreach ($instrutores as $instrutor): ?>
                                    <option value="<?php echo $instrutor->getId(); ?>">
                                        <?php echo htmlspecialchars($instrutor->getNome()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nome da Turma</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data</label>
                            <input type="date" name="data" id="turma_data" class="form-control" required onchange="verificarHorariosDisponiveis()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Horário de Início</label>
                            <select name="hora_inicio" id="hora_inicio" class="form-select" required onchange="calcularHoraFim()">
                                <option value="">Selecione o horário</option>
                            </select>
                            <small class="text-muted">Horários disponíveis de 30 em 30 minutos (24h)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duração (em minutos)</label>
                            <select name="duracao" id="duracao" class="form-select" required onchange="calcularHoraFim()">
                                <option value="30">30 minutos</option>
                                <option value="60">1 hora</option>
                                <option value="90">1 hora e 30 minutos</option>
                                <option value="120">2 horas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Horário de Fim (calculado automaticamente)</label>
                            <input type="text" id="hora_fim_display" class="form-control" readonly>
                            <input type="hidden" name="data_inicio" id="data_inicio_hidden">
                            <input type="hidden" name="data_fim" id="data_fim_hidden">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dias da Semana (Recorrente)</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias_semana[]" value="1" id="dia_seg">
                                <label class="form-check-label" for="dia_seg">Segunda-feira</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias_semana[]" value="2" id="dia_ter">
                                <label class="form-check-label" for="dia_ter">Terça-feira</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias_semana[]" value="3" id="dia_qua">
                                <label class="form-check-label" for="dia_qua">Quarta-feira</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias_semana[]" value="4" id="dia_qui">
                                <label class="form-check-label" for="dia_qui">Quinta-feira</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias_semana[]" value="5" id="dia_sex">
                                <label class="form-check-label" for="dia_sex">Sexta-feira</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias_semana[]" value="6" id="dia_sab">
                                <label class="form-check-label" for="dia_sab">Sábado</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias_semana[]" value="0" id="dia_dom">
                                <label class="form-check-label" for="dia_dom">Domingo</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data Final (até quando a turma ocorrerá)</label>
                            <input type="date" name="data_fim_turma" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacidade Máxima</label>
                            <input type="number" name="capacidade_maxima" class="form-control" value="20" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Treino -->
    <div class="modal fade" id="modalTreino" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Treino</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo getActionUrl('treino-cadastrar'); ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Aluno</label>
                            <select name="id_usuario" class="form-select" required>
                                <option value="">Selecione um aluno</option>
                                <?php foreach ($alunos as $aluno): ?>
                                    <option value="<?php echo $aluno->getId(); ?>">
                                        <?php echo htmlspecialchars($aluno->getNome()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Dieta -->
    <div class="modal fade" id="modalDieta" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Dieta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo getActionUrl('dieta-cadastrar'); ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Aluno</label>
                            <select name="id_usuario" class="form-select" required>
                                <option value="">Selecione um aluno</option>
                                <?php foreach ($alunos as $aluno): ?>
                                    <option value="<?php echo $aluno->getId(); ?>">
                                        <?php echo htmlspecialchars($aluno->getNome()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuário -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuário</h5>
                    <button type="button" class="btn-close" onclick="fecharModalEditarUsuario()" aria-label="Close"></button>
                </div>
                <form action="<?php echo getActionUrl('usuario-atualizar'); ?>" method="POST" id="formEditarUsuario">
                    <input type="hidden" name="id" id="usuario_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" name="nome" id="usuario_nome" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="usuario_email" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CPF</label>
                                <input type="text" name="cpf" id="usuario_cpf" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="telefone" id="usuario_telefone" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipo</label>
                                <select name="tipo" id="usuario_tipo" class="form-select" required>
                                    <option value="1">1 - Admin</option>
                                    <option value="2">2 - Instrutor</option>
                                    <option value="3">3 - Aluno</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Estado</label>
                                <input type="text" name="estado" id="usuario_estado" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cidade</label>
                                <input type="text" name="cidade" id="usuario_cidade" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bairro</label>
                                <input type="text" name="bairro" id="usuario_bairro" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rua</label>
                                <input type="text" name="rua" id="usuario_rua" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nova Senha (deixe em branco para não alterar)</label>
                            <input type="password" name="senha" id="usuario_senha" class="form-control">
                            <small class="text-muted">Deixe em branco se não quiser alterar a senha</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="fecharModalEditarUsuario()">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMensagem" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMensagemTitulo">Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalMensagemTexto">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostrarMensagem(titulo, mensagem) {
            document.getElementById('modalMensagemTitulo').textContent = titulo;
            document.getElementById('modalMensagemTexto').textContent = mensagem;
            const modal = new bootstrap.Modal(document.getElementById('modalMensagem'));
            modal.show();
        }

        function editarUnidade(id) {
            mostrarMensagem('Em Desenvolvimento', 'Funcionalidade de edição em desenvolvimento');
        }
        function editarCurso(id) {
            mostrarMensagem('Em Desenvolvimento', 'Funcionalidade de edição em desenvolvimento');
        }
        function editarPlano(id) {
            mostrarMensagem('Em Desenvolvimento', 'Funcionalidade de edição em desenvolvimento');
        }
        function editarTurma(id) {
            mostrarMensagem('Em Desenvolvimento', 'Funcionalidade de edição em desenvolvimento');
        }
        function verTurma(id) {
            window.location.href = 'cursos.php?turma=' + id;
        }
        
        function gerarHorarios() {
            const select = document.getElementById('hora_inicio');
            if (!select) return;
            select.innerHTML = '<option value="">Selecione o horário</option>';
            
            for (let hora = 0; hora < 24; hora++) {
                for (let minuto = 0; minuto < 60; minuto += 30) {
                    const horaStr = String(hora).padStart(2, '0');
                    const minutoStr = String(minuto).padStart(2, '0');
                    const valor = horaStr + ':' + minutoStr;
                    const texto = horaStr + ':' + minutoStr;
                    
                    const option = document.createElement('option');
                    option.value = valor;
                    option.textContent = texto;
                    select.appendChild(option);
                }
            }
        }
        
        function verificarHorariosDisponiveis() {
            const data = document.getElementById('turma_data');
            if (!data || !data.value) return;
            
            fetch('<?php echo getActionUrl('turma-horarios-disponiveis'); ?>?data=' + data.value)
                .then(response => response.json())
                .then(horariosOcupados => {
                    const select = document.getElementById('hora_inicio');
                    if (!select) return;
                    const options = select.querySelectorAll('option');
                    
                    options.forEach(option => {
                        if (option.value) {
                            const horaCompleta = data.value + ' ' + option.value + ':00';
                            const ocupado = horariosOcupados.some(ocupado => {
                                const inicioOcupado = new Date(ocupado.data_inicio);
                                const fimOcupado = new Date(ocupado.data_fim);
                                const horaCheck = new Date(horaCompleta);
                                return horaCheck >= inicioOcupado && horaCheck < fimOcupado;
                            });
                            
                            if (ocupado) {
                                option.disabled = true;
                                option.textContent = option.value + ' (Ocupado)';
                                option.style.color = '#dc3545';
                            } else {
                                option.disabled = false;
                                option.style.color = '';
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Erro ao verificar horários:', error);
                });
        }
        
        function calcularHoraFim() {
            const horaInicio = document.getElementById('hora_inicio');
            const duracao = document.getElementById('duracao');
            const data = document.getElementById('turma_data');
            
            if (!horaInicio || !horaInicio.value || !data || !data.value || !duracao) return;
            
            const [hora, minuto] = horaInicio.value.split(':').map(Number);
            const inicio = new Date(data.value + 'T' + horaInicio.value + ':00');
            const fim = new Date(inicio.getTime() + parseInt(duracao.value) * 60000);
            
            const horaFim = String(fim.getHours()).padStart(2, '0') + ':' + String(fim.getMinutes()).padStart(2, '0');
            const horaFimDisplay = document.getElementById('hora_fim_display');
            const dataInicioHidden = document.getElementById('data_inicio_hidden');
            const dataFimHidden = document.getElementById('data_fim_hidden');
            
            if (horaFimDisplay) horaFimDisplay.value = horaFim;
            if (dataInicioHidden) dataInicioHidden.value = data.value + ' ' + horaInicio.value + ':00';
            if (dataFimHidden) dataFimHidden.value = data.value + ' ' + horaFim + ':00';
        }
        
        // Usa delegação de eventos para garantir que funcione mesmo com elementos dinâmicos
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-editar-usuario')) {
                const btn = e.target.closest('.btn-editar-usuario');
                window.abrirModalEditarUsuario(btn, e);
            }
        });
        
        // Também adiciona event listeners diretos quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            // Adiciona listeners diretos aos botões existentes
            setTimeout(function() {
                const botoes = document.querySelectorAll('.btn-editar-usuario');
                console.log('Botões encontrados no DOMContentLoaded:', botoes.length);
                botoes.forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.abrirModalEditarUsuario(this, e);
                    });
                });
            }, 500);
        });
        
        window.mostrarConfirmacao = function(titulo, texto, callback) {
            document.getElementById('modalConfirmacaoTitulo').textContent = titulo;
            document.getElementById('modalConfirmacaoTexto').textContent = texto;
            const btnConfirmar = document.getElementById('modalConfirmacaoBtn');
            btnConfirmar.onclick = function() {
                callback();
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacao'));
                modal.hide();
            };
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            modal.show();
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            
            const modalTurma = document.getElementById('modalTurma');
            if (modalTurma) {
                modalTurma.addEventListener('show.bs.modal', function() {
                    setTimeout(gerarHorarios, 100);
                });
            }
            
            document.querySelectorAll('.form-deletar').forEach(form => {
                const btnDeletar = form.querySelector('.btn-deletar') || form.querySelector('button[type="submit"]');
                if (btnDeletar) {
                    if (btnDeletar.type === 'submit') {
                        btnDeletar.type = 'button';
                    }
                    btnDeletar.addEventListener('click', function() {
                        const tipo = form.getAttribute('data-tipo');
                        const nome = form.getAttribute('data-nome');
                        const mensagem = `Tem certeza que deseja deletar este ${tipo}${nome ? ' (' + nome + ')' : ''}?`;
                        window.mostrarConfirmacao('Confirmar Exclusão', mensagem, () => {
                            form.submit();
                        });
                    });
                }
            });
        });
            
    </script>
</body>
</html>
