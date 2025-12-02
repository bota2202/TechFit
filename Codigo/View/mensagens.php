<?php
/**
 * Mensagens - TechFit
 */

session_start();

require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';
require_once __DIR__ . '/../Model/Conexao.php';
require_once __DIR__ . '/../Model/MensagemDAO.php';
require_once __DIR__ . '/../Model/UsuarioDAO.php';
require_once __DIR__ . '/../Model/TurmaDAO.php';

Auth::requireAuth();

$usuario = $_SESSION['usuario'];
$tipoUsuario = $usuario['tipo'] ?? TIPO_USUARIO_ALUNO;
$mensagemDAO = new MensagemDAO();
$usuarioDAO = new UsuarioDAO();
$turmaDAO = new TurmaDAO();

// Busca mensagens do usuário
$mensagens = $mensagemDAO->readByDestinatario($usuario['id']);
$naoLidas = array_filter($mensagens, function($m) { return !$m->getLida(); });

// Se for admin, busca dados para enviar mensagens
$podeEnviar = Auth::isAdmin();
$alunos = [];
$turmas = [];
if ($podeEnviar) {
    $todosUsuarios = $usuarioDAO->readAll();
    foreach ($todosUsuarios as $u) {
        if ($u->getTipo() == TIPO_USUARIO_ALUNO) {
            $alunos[] = $u;
        }
    }
    $turmas = $turmaDAO->readAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Mensagens</title>
    <link rel="stylesheet" href="../Public/css/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding-top: 80px;
            background: #f5f7fa;
        }
        .mensagens-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .mensagem-item {
            cursor: pointer;
            transition: background 0.2s;
        }
        .mensagem-item:hover {
            background: #f8f9fa;
        }
        .mensagem-nao-lida {
            background: #e7f3ff;
            font-weight: 600;
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
            <?php if ($tipoUsuario == TIPO_USUARIO_ALUNO): ?>
                <a class="btn-nav-centro btn-ativo" href="dashboard.php">Área do Aluno</a>
            <?php endif; ?>
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
                    <a href="dashboard.php" class="usuario-dropdown-item">Área do Aluno</a>
                    <a href="../../index.php?action=logout" class="usuario-dropdown-item logout">
                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                    </a>
                </div>
            </div>
        </section>
    </nav>

    <div class="mensagens-container">
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

        <div class="row">
            <div class="col-md-<?php echo $podeEnviar ? '8' : '12'; ?>">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between">
                        <h4><i class="fas fa-envelope me-2"></i>Minhas Mensagens</h4>
                        <?php if (count($naoLidas) > 0): ?>
                            <span class="badge bg-warning text-dark"><?php echo count($naoLidas); ?> não lidas</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($mensagens)): ?>
                            <?php foreach ($mensagens as $mensagem): ?>
                                <?php 
                                // Busca dados do remetente
                                $remetente = $usuarioDAO->readById($mensagem->getIdRemetente());
                                ?>
                                <div class="mensagem-item mensagem-<?php echo $mensagem->getLida() ? 'lida' : 'nao-lida'; ?> p-3 mb-2 border rounded" 
                                     onclick="window.location.href='chat.php?usuario=<?php echo $mensagem->getIdRemetente(); ?>'"
                                     style="cursor: pointer;">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6>
                                                <?php if ($remetente): ?>
                                                    <?php echo htmlspecialchars($remetente->getNome()); ?>
                                                <?php else: ?>
                                                    <?php echo htmlspecialchars($mensagem->getAssunto()); ?>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1"><?php echo htmlspecialchars(substr($mensagem->getConteudo(), 0, 100)); ?>...</p>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($mensagem->getDataEnvio())); ?>
                                            </small>
                                        </div>
                                        <div>
                                            <?php if (!$mensagem->getLida()): ?>
                                                <span class="badge bg-primary">Nova</span>
                                            <?php endif; ?>
                                            <i class="fas fa-chevron-right ms-2 text-muted"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Nenhuma mensagem recebida.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($podeEnviar): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-paper-plane me-2"></i>Enviar Mensagem</h5>
                        </div>
                        <div class="card-body">
                            <form action="../../index.php?action=enviar-mensagem" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Envio</label>
                                    <select name="tipo" class="form-select" id="tipo-envio" onchange="toggleDestinatario()">
                                        <option value="geral">Individual</option>
                                        <option value="turma">Para Turma</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="div-destinatario">
                                    <label class="form-label">Destinatário</label>
                                    <select name="id_destinatario" class="form-select">
                                        <option value="">Selecione um aluno</option>
                                        <?php foreach ($alunos as $aluno): ?>
                                            <option value="<?php echo $aluno->getId(); ?>">
                                                <?php echo htmlspecialchars($aluno->getNome()); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3" id="div-turma" style="display:none;">
                                    <label class="form-label">Turma</label>
                                    <select name="id_turma" class="form-select">
                                        <option value="">Selecione uma turma</option>
                                        <?php foreach ($turmas as $turma): ?>
                                            <option value="<?php echo $turma->getId(); ?>">
                                                <?php echo htmlspecialchars($turma->getNome()); ?>
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

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleDestinatario() {
            const tipo = document.getElementById('tipo-envio').value;
            const divDestinatario = document.getElementById('div-destinatario');
            const divTurma = document.getElementById('div-turma');
            
            if (tipo === 'turma') {
                divDestinatario.style.display = 'none';
                divTurma.style.display = 'block';
                divTurma.querySelector('select').required = true;
                divDestinatario.querySelector('select').required = false;
            } else {
                divDestinatario.style.display = 'block';
                divTurma.style.display = 'none';
                divDestinatario.querySelector('select').required = true;
                divTurma.querySelector('select').required = false;
            }
        }

        // Removido - agora clica na mensagem para abrir o chat
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

