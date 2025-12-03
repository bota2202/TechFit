<?php

session_start();

require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';
require_once __DIR__ . '/../Model/Conexao.php';
require_once __DIR__ . '/../Model/MensagemDAO.php';
require_once __DIR__ . '/../Model/UsuarioDAO.php';
require_once __DIR__ . '/../Model/TurmaDAO.php';
require_once __DIR__ . '/../Model/helpers.php';

Auth::requireAuth();

$usuario = $_SESSION['usuario'];
$tipoUsuario = $usuario['tipo'] ?? TIPO_USUARIO_ALUNO;
$mensagemDAO = new MensagemDAO();
$usuarioDAO = new UsuarioDAO();
$turmaDAO = new TurmaDAO();

$conversas = $mensagemDAO->readConversas($usuario['id']);
$mensagens = $mensagemDAO->readByDestinatario($usuario['id']);
$naoLidas = array_filter($mensagens, function($m) { return !$m->getLida(); });

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

$idConversaSelecionada = isset($_GET['usuario']) ? intval($_GET['usuario']) : null;
$conversaAtiva = null;
$outroUsuario = null;
$tituloConversa = 'Nova conversa';

if ($idConversaSelecionada) {
    $outroUsuario = $usuarioDAO->readById($idConversaSelecionada);
    if ($outroUsuario) {
        $conversaAtiva = $mensagemDAO->readConversa($usuario['id'], $idConversaSelecionada);
        $tituloConversa = $mensagemDAO->getTituloConversa($usuario['id'], $idConversaSelecionada);
        
        foreach ($conversaAtiva as $msg) {
            if ($msg->getIdDestinatario() == $usuario['id'] && !$msg->getLida()) {
                $mensagemDAO->marcarComoLida($msg->getId());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Mensagens</title>
    <link rel="icon" type="image/svg+xml" href="../Public/favicon.svg">
    <link rel="alternate icon" href="../Public/favicon.svg">
    <link rel="stylesheet" href="../Public/css/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding-top: 80px;
            background: #fafafa;
            height: calc(100vh - 80px);
            overflow: hidden;
        }
        .direct-container {
            max-width: 1200px;
            height: calc(100vh - 80px);
            margin: 0 auto;
            background: white;
            border: 1px solid #dbdbdb;
            border-radius: 4px;
            display: flex;
            overflow: hidden;
        }
        .direct-sidebar {
            width: 350px;
            border-right: 1px solid #dbdbdb;
            display: flex;
            flex-direction: column;
            background: white;
        }
        .direct-header {
            padding: 20px;
            border-bottom: 1px solid #dbdbdb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .direct-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 16px;
        }
        .direct-header .btn-nova-msg {
            background: none;
            border: none;
            font-size: 24px;
            color: #262626;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .direct-header .btn-nova-msg:hover {
            color: #8e8e8e;
        }
        .direct-conversas {
            flex: 1;
            overflow-y: auto;
        }
        .conversa-item-direct {
            padding: 12px 20px;
            border-bottom: 1px solid #dbdbdb;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background 0.2s;
            text-decoration: none;
            color: inherit;
        }
        .conversa-item-direct:hover {
            background: #fafafa;
        }
        .conversa-item-direct.active {
            background: #fafafa;
            border-left: 2px solid #262626;
        }
        .conversa-avatar-direct {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 20px;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .conversa-info {
            flex: 1;
            min-width: 0;
        }
        .conversa-nome {
            font-weight: 600;
            font-size: 14px;
            color: #262626;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .conversa-titulo {
            font-size: 12px;
            color: #8e8e8e;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .conversa-data {
            font-size: 12px;
            color: #8e8e8e;
            margin-top: 2px;
        }
        .direct-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }
        .direct-chat-header {
            padding: 16px 20px;
            border-bottom: 1px solid #dbdbdb;
            display: flex;
            align-items: center;
        }
        .direct-chat-header .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
            margin-right: 12px;
        }
        .direct-chat-header .info h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #262626;
        }
        .direct-chat-header .info small {
            font-size: 12px;
            color: #8e8e8e;
        }
        .direct-chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #fafafa;
        }
        .mensagem-bubble {
            margin-bottom: 12px;
            display: flex;
        }
        .mensagem-bubble.enviada {
            justify-content: flex-end;
        }
        .mensagem-bubble.recebida {
            justify-content: flex-start;
        }
        .bubble {
            max-width: 65%;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.4;
            word-wrap: break-word;
        }
        .bubble.enviada {
            background: #efefef;
            color: #262626;
        }
        .bubble.recebida {
            background: #ffffff;
            color: #262626;
            border: 1px solid #dbdbdb;
        }
        .bubble-time {
            font-size: 11px;
            color: #8e8e8e;
            margin-top: 4px;
            text-align: right;
        }
        .direct-chat-input {
            padding: 16px 20px;
            border-top: 1px solid #dbdbdb;
            background: white;
        }
        .direct-chat-form {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .direct-chat-form textarea {
            flex: 1;
            border: 1px solid #dbdbdb;
            border-radius: 22px;
            padding: 10px 16px;
            resize: none;
            font-size: 14px;
            max-height: 100px;
        }
        .direct-chat-form textarea:focus {
            outline: none;
            border-color: #8e8e8e;
        }
        .direct-chat-form button {
            background: none;
            border: none;
            color: #0095f6;
            font-size: 16px;
            cursor: pointer;
            padding: 8px;
        }
        .direct-chat-form button:hover {
            color: #1877f2;
        }
        .direct-chat-form button:disabled {
            color: #c7c7c7;
            cursor: not-allowed;
        }
        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #8e8e8e;
            padding: 40px;
        }
        .empty-state i {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        .empty-state h5 {
            font-size: 20px;
            font-weight: 300;
            margin-bottom: 8px;
        }
        .empty-state p {
            font-size: 14px;
        }
        .modal-nova-msg {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-nova-msg.active {
            display: flex;
        }
        .modal-content-custom {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .modal-header-custom {
            padding: 16px 20px;
            border-bottom: 1px solid #dbdbdb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header-custom h5 {
            margin: 0;
            font-weight: 600;
            font-size: 16px;
        }
        .modal-header-custom .btn-close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #262626;
        }
        .modal-body-custom {
            padding: 20px;
        }
        .alert-custom {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 2000;
            min-width: 300px;
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
            <?php elseif ($tipoUsuario == TIPO_USUARIO_ADMIN): ?>
                <a class="btn-nav-centro" href="dashboard_admin.php">Dashboard Admin</a>
            <?php elseif ($tipoUsuario == TIPO_USUARIO_INSTRUTOR): ?>
                <a class="btn-nav-centro" href="dashboard_instrutor.php">Dashboard Instrutor</a>
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

    <?php
    if (isset($_SESSION['erro'])) {
        echo '<div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['erro']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['erro']);
    }
    if (isset($_SESSION['sucesso'])) {
        echo '<div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['sucesso']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['sucesso']);
    }
    ?>

    <div class="direct-container">
        <div class="direct-sidebar">
            <div class="direct-header">
                <h5><?php echo htmlspecialchars($usuario['nome']); ?></h5>
                <?php if ($podeEnviar): ?>
                <button class="btn-nova-msg" onclick="abrirModalNovaMsg()" title="Nova mensagem">
                    <i class="fas fa-plus"></i>
                </button>
                <?php endif; ?>
            </div>
            <div class="direct-conversas">
                <?php if (!empty($conversas)): ?>
                    <?php foreach ($conversas as $conv): ?>
                        <?php 
                        $outro = $usuarioDAO->readById($conv['outro_usuario']);
                        if (!$outro) continue;
                        $isActive = $conv['outro_usuario'] == $idConversaSelecionada;
                        ?>
                        <a href="mensagens.php?usuario=<?php echo $conv['outro_usuario']; ?>" 
                           class="conversa-item-direct <?php echo $isActive ? 'active' : ''; ?>">
                            <div class="conversa-avatar-direct">
                                <?php echo strtoupper(substr($outro->getNome(), 0, 1)); ?>
                            </div>
                            <div class="conversa-info">
                                <div class="conversa-nome"><?php echo htmlspecialchars($outro->getNome()); ?></div>
                                <div class="conversa-titulo"><?php echo htmlspecialchars($conv['titulo_conversa'] ?? 'Nova conversa'); ?></div>
                                <div class="conversa-data"><?php echo date('d/m/Y H:i', strtotime($conv['ultima_mensagem'])); ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <h5>Nenhuma conversa</h5>
                        <p>Inicie uma nova conversa para começar</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="direct-main">
            <?php if ($outroUsuario && $conversaAtiva !== null): ?>
                <div class="direct-chat-header">
                    <div class="avatar">
                        <?php echo strtoupper(substr($outroUsuario->getNome(), 0, 1)); ?>
                    </div>
                    <div class="info">
                        <h6><?php echo htmlspecialchars($outroUsuario->getNome()); ?></h6>
                        <small><?php echo htmlspecialchars($tituloConversa); ?></small>
                    </div>
                </div>
                <div class="direct-chat-messages" id="chatMessages">
                    <?php foreach ($conversaAtiva as $msg): ?>
                        <?php $isEnviada = $msg->getIdRemetente() == $usuario['id']; ?>
                        <div class="mensagem-bubble <?php echo $isEnviada ? 'enviada' : 'recebida'; ?>">
                            <div class="bubble <?php echo $isEnviada ? 'enviada' : 'recebida'; ?>">
                                <?php echo nl2br(htmlspecialchars($msg->getConteudo())); ?>
                                <div class="bubble-time">
                                    <?php echo date('H:i', strtotime($msg->getDataEnvio())); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="direct-chat-input">
                    <form action="<?php echo getActionUrl('responder-mensagem'); ?>" method="POST" id="chatForm">
                        <input type="hidden" name="id_destinatario" value="<?php echo $idConversaSelecionada; ?>">
                        <input type="hidden" name="assunto" value="<?php echo htmlspecialchars($tituloConversa); ?>">
                        <div class="direct-chat-form">
                            <textarea name="conteudo" id="mensagemInput" rows="1" placeholder="Mensagem..." required></textarea>
                            <button type="submit" id="btnEnviar">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <h5>Suas mensagens</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($podeEnviar): ?>
    <div class="modal-nova-msg" id="modalNovaMsg">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h5>Nova Mensagem</h5>
                <button class="btn-close-modal" onclick="fecharModalNovaMsg()">&times;</button>
            </div>
            <div class="modal-body-custom">
                <form action="<?php echo getActionUrl('enviar-mensagem'); ?>" method="POST">
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

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane me-2"></i>Enviar
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        const chatMessages = document.getElementById('chatMessages');
        const mensagemInput = document.getElementById('mensagemInput');
        let isTyping = false;
        let refreshInterval;

        function scrollToBottom() {
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        scrollToBottom();

        function startAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            if (<?php echo $idConversaSelecionada ? 'true' : 'false'; ?>) {
                refreshInterval = setInterval(function() {
                    const modal = document.getElementById('modalNovaMsg');
                    const isModalOpen = modal && modal.classList.contains('active');
                    if (!isTyping && document.activeElement !== mensagemInput && !isModalOpen) {
                        location.reload();
                    }
                }, 5000);
            }
        }

        if (mensagemInput) {
            mensagemInput.addEventListener('focus', function() {
                isTyping = true;
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
            });

            mensagemInput.addEventListener('blur', function() {
                isTyping = false;
                startAutoRefresh();
            });

            mensagemInput.addEventListener('input', function() {
                isTyping = true;
                clearTimeout(window.typingTimeout);
                window.typingTimeout = setTimeout(function() {
                    isTyping = false;
                }, 2000);
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            mensagemInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    isTyping = false;
                    document.getElementById('chatForm').submit();
                }
            });
        }

        function abrirModalNovaMsg() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            document.getElementById('modalNovaMsg').classList.add('active');
        }

        function fecharModalNovaMsg() {
            document.getElementById('modalNovaMsg').classList.remove('active');
            startAutoRefresh();
        }

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

        window.addEventListener('click', function(e) {
            const modal = document.getElementById('modalNovaMsg');
            if (e.target === modal) {
                fecharModalNovaMsg();
            }
        });

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-custom');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 7000);

        startAutoRefresh();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
