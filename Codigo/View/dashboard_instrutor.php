<?php

session_start();

require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';
require_once __DIR__ . '/../Model/Conexao.php';
require_once __DIR__ . '/../Model/TreinoDAO.php';
require_once __DIR__ . '/../Model/DietaDAO.php';
require_once __DIR__ . '/../Model/UsuarioDAO.php';
require_once __DIR__ . '/../Model/MensagemDAO.php';
require_once __DIR__ . '/../Model/helpers.php';

Auth::requireAuth();

$usuario = $_SESSION['usuario'];
$tipoUsuario = $usuario['tipo'] ?? TIPO_USUARIO_ALUNO;

if ($tipoUsuario != TIPO_USUARIO_INSTRUTOR) {
    if ($tipoUsuario == TIPO_USUARIO_ADMIN) {
        header('Location: dashboard_admin.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$treinoDAO = new TreinoDAO();
$dietaDAO = new DietaDAO();
$usuarioDAO = new UsuarioDAO();
$mensagemDAO = new MensagemDAO();

$alunos = [];
$todosUsuarios = $usuarioDAO->readAll();
foreach ($todosUsuarios as $u) {
    if ($u->getTipo() == TIPO_USUARIO_ALUNO) {
        $alunos[] = $u;
    }
}

$todosTreinos = [];
$todasDietas = [];
foreach ($alunos as $aluno) {
    $treinosAluno = $treinoDAO->readByUsuarioId($aluno->getId());
    foreach ($treinosAluno as $treino) {
        $todosTreinos[] = ['treino' => $treino, 'aluno' => $aluno];
    }
    $dietasAluno = $dietaDAO->readByUsuarioId($aluno->getId());
    foreach ($dietasAluno as $dieta) {
        $todasDietas[] = ['dieta' => $dieta, 'aluno' => $aluno];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Dashboard Instrutor</title>
    <link rel="icon" type="image/svg+xml" href="../Public/favicon.svg">
    <link rel="alternate icon" href="../Public/favicon.svg">
    <link rel="stylesheet" href="../Public/css/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px;
        }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .nav-tabs .nav-link {
            color: #666;
            border: none;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: #667eea;
            border-bottom: 2px solid #667eea;
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
            <a class="btn-nav-centro btn-ativo" href="dashboard_instrutor.php">Dashboard Instrutor</a>
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
                <h1><i class="fas fa-user-tie me-2"></i>Dashboard Instrutor</h1>
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

        <ul class="nav nav-tabs mb-3" id="instrutorTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="treinos-tab" data-bs-toggle="tab" data-bs-target="#treinos" type="button" role="tab">
                    <i class="fas fa-dumbbell me-2"></i>Treinos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dietas-tab" data-bs-toggle="tab" data-bs-target="#dietas" type="button" role="tab">
                    <i class="fas fa-utensils me-2"></i>Dietas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mensagens-tab" data-bs-toggle="tab" data-bs-target="#mensagens" type="button" role="tab">
                    <i class="fas fa-envelope me-2"></i>Mensagens
                </button>
            </li>
        </ul>

        <div class="tab-content" id="instrutorTabContent">
            <!-- Aba Treinos -->
            <div class="tab-pane fade show active" id="treinos" role="tabpanel">
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
                            <?php if (!empty($todosTreinos)): ?>
                                <?php foreach ($todosTreinos as $item): ?>
                                    <tr>
                                        <td><?php echo $item['treino']['id']; ?></td>
                                        <td><?php echo htmlspecialchars($item['aluno']->getNome()); ?></td>
                                        <td><?php echo htmlspecialchars($item['treino']['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($item['treino']['descricao'], 0, 50)); ?>...</td>
                                        <td><?php echo date('d/m/Y', strtotime($item['treino']['data_criacao'])); ?></td>
                                        <td class="table-actions">
                                            <form action="<?php echo getActionUrl('treino-deletar'); ?>" method="POST" style="display:inline;" class="form-deletar" data-tipo="treino" data-nome="<?php echo htmlspecialchars($item['treino']['titulo']); ?>">
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
                            <?php if (!empty($todasDietas)): ?>
                                <?php foreach ($todasDietas as $item): ?>
                                    <tr>
                                        <td><?php echo $item['dieta']['id']; ?></td>
                                        <td><?php echo htmlspecialchars($item['aluno']->getNome()); ?></td>
                                        <td><?php echo htmlspecialchars($item['dieta']['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($item['dieta']['descricao'], 0, 50)); ?>...</td>
                                        <td><?php echo date('d/m/Y', strtotime($item['dieta']['data_criacao'])); ?></td>
                                        <td class="table-actions">
                                            <form action="<?php echo getActionUrl('dieta-deletar'); ?>" method="POST" style="display:inline;" class="form-deletar" data-tipo="dieta" data-nome="<?php echo htmlspecialchars($item['dieta']['titulo']); ?>">
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

            <!-- Aba Mensagens -->
            <div class="tab-pane fade" id="mensagens" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Enviar Mensagens</h4>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form action="<?php echo getActionUrl('enviar-mensagem'); ?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Destinatário</label>
                                <select name="id_destinatario" class="form-select" required>
                                    <option value="">Selecione um aluno</option>
                                    <?php foreach ($alunos as $aluno): ?>
                                        <option value="<?php echo $aluno->getId(); ?>">
                                            <?php echo htmlspecialchars($aluno->getNome()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Assunto</label>
                                <input type="text" name="assunto" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mensagem</label>
                                <textarea name="conteudo" class="form-control" rows="4" required></textarea>
                            </div>
                            <input type="hidden" name="tipo" value="geral">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem
                            </button>
                        </form>
                    </div>
                </div>
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

    <div class="modal fade" id="modalConfirmacao" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmacaoTitulo">Confirmar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalConfirmacaoTexto">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="modalConfirmacaoBtn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostrarConfirmacao(titulo, texto, callback) {
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
        }
        
        document.querySelectorAll('.form-deletar').forEach(form => {
            const btnDeletar = form.querySelector('.btn-deletar');
            if (btnDeletar) {
                btnDeletar.addEventListener('click', function() {
                    const tipo = form.getAttribute('data-tipo');
                    const nome = form.getAttribute('data-nome');
                    const mensagem = `Tem certeza que deseja deletar este ${tipo}${nome ? ' (' + nome + ')' : ''}?`;
                    mostrarConfirmacao('Confirmar Exclusão', mensagem, () => {
                        form.submit();
                    });
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

