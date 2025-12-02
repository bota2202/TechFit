<?php
/**
 * Chat/Conversa - TechFit
 */

session_start();

require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';
require_once __DIR__ . '/../Model/Conexao.php';
require_once __DIR__ . '/../Model/MensagemDAO.php';
require_once __DIR__ . '/../Model/UsuarioDAO.php';
require_once __DIR__ . '/../Model/helpers.php';

Auth::requireAuth();

$usuario = $_SESSION['usuario'];
$tipoUsuario = $usuario['tipo'] ?? TIPO_USUARIO_ALUNO;
$mensagemDAO = new MensagemDAO();
$usuarioDAO = new UsuarioDAO();

// ID do outro usuário na conversa
$idOutroUsuario = intval($_GET['usuario'] ?? 0);

if (!$idOutroUsuario) {
    $_SESSION['erro'] = 'Usuário não especificado';
    header('Location: ' . getViewUrl('mensagens.php'));
    exit;
}

// Busca dados do outro usuário
$outroUsuario = $usuarioDAO->readById($idOutroUsuario);
if (!$outroUsuario) {
    $_SESSION['erro'] = 'Usuário não encontrado';
    header('Location: ' . getViewUrl('mensagens.php'));
    exit;
}

// Busca conversa entre os dois usuários
$conversa = $mensagemDAO->readConversa($usuario['id'], $idOutroUsuario);

// Marca mensagens recebidas como lidas
foreach ($conversa as $msg) {
    if ($msg->getIdDestinatario() == $usuario['id'] && !$msg->getLida()) {
        $mensagemDAO->marcarComoLida($msg->getId());
    }
}

// Busca lista de conversas para sidebar
$conversas = $mensagemDAO->readConversas($usuario['id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Chat</title>
    <link rel="stylesheet" href="../Public/css/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding-top: 80px;
            background: #f5f7fa;
            height: calc(100vh - 80px);
            overflow: hidden;
        }
        .chat-container {
            max-width: 1400px;
            margin: 0 auto;
            height: calc(100vh - 100px);
            display: flex;
            gap: 20px;
            padding: 20px;
        }
        .chat-sidebar {
            width: 300px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            max-height: 100%;
        }
        .chat-main {
            flex: 1;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .message {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }
        .message.own {
            flex-direction: row-reverse;
        }
        .message-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            word-wrap: break-word;
        }
        .message.own .message-bubble {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        .message.other .message-bubble {
            background: white;
            color: #333;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .message-time {
            font-size: 0.75rem;
            color: #999;
            margin-top: 5px;
        }
        .message.own .message-time {
            text-align: right;
        }
        .chat-input {
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            background: white;
            border-radius: 0 0 10px 10px;
        }
        .conversa-item {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            cursor: pointer;
            transition: background 0.2s;
        }
        .conversa-item:hover {
            background: #f8f9fa;
        }
        .conversa-item.active {
            background: #e7f3ff;
            border-left: 4px solid #11998e;
        }
        .conversa-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
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
                    <?php if ($tipoUsuario == TIPO_USUARIO_ALUNO): ?>
                        <a href="dashboard.php" class="usuario-dropdown-item">Área do Aluno</a>
                    <?php elseif ($tipoUsuario == TIPO_USUARIO_ADMIN): ?>
                        <a href="dashboard_admin.php" class="usuario-dropdown-item">Dashboard Admin</a>
                    <?php endif; ?>
                    <a href="../../index.php?action=logout" class="usuario-dropdown-item logout">
                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                    </a>
                </div>
            </div>
        </section>
    </nav>

    <div class="chat-container">
        <!-- Sidebar com lista de conversas -->
        <div class="chat-sidebar">
            <div class="p-3 border-bottom">
                <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Conversas</h5>
            </div>
            <?php if (!empty($conversas)): ?>
                <?php foreach ($conversas as $conv): ?>
                    <?php 
                    $outro = $usuarioDAO->readById($conv['outro_usuario']);
                    if (!$outro) continue;
                    $isActive = $conv['outro_usuario'] == $idOutroUsuario;
                    ?>
                    <a href="chat.php?usuario=<?php echo $conv['outro_usuario']; ?>" 
                       class="conversa-item <?php echo $isActive ? 'active' : ''; ?>" 
                       style="text-decoration: none; color: inherit; display: block;">
                        <div class="d-flex align-items-center">
                            <div class="conversa-avatar">
                                <?php echo strtoupper(substr($outro->getNome(), 0, 1)); ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold"><?php echo htmlspecialchars($outro->getNome()); ?></div>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($conv['ultima_mensagem'])); ?>
                                </small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-3 text-center text-muted">
                    <p>Nenhuma conversa ainda</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Área principal do chat -->
        <div class="chat-main">
            <div class="chat-header">
                <div class="d-flex align-items-center">
                    <div class="conversa-avatar me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                        <?php echo strtoupper(substr($outroUsuario->getNome(), 0, 1)); ?>
                    </div>
                    <div>
                        <h5 class="mb-0"><?php echo htmlspecialchars($outroUsuario->getNome()); ?></h5>
                        <small>Conversa</small>
                    </div>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <?php if (!empty($conversa)): ?>
                    <?php foreach ($conversa as $msg): ?>
                        <?php $isOwn = $msg->getIdRemetente() == $usuario['id']; ?>
                        <div class="message <?php echo $isOwn ? 'own' : 'other'; ?>">
                            <div>
                                <div class="message-bubble">
                                    <?php if (!$isOwn && $msg->getAssunto() != 'Nova mensagem'): ?>
                                        <div class="fw-bold mb-1" style="font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($msg->getAssunto()); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div><?php echo nl2br(htmlspecialchars($msg->getConteudo())); ?></div>
                                </div>
                                <div class="message-time">
                                    <?php echo date('d/m/Y H:i', strtotime($msg->getDataEnvio())); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted mt-5">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>Nenhuma mensagem ainda. Inicie a conversa!</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="chat-input">
                <?php
                if (isset($_SESSION['erro'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">' . htmlspecialchars($_SESSION['erro']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    unset($_SESSION['erro']);
                }
                if (isset($_SESSION['sucesso'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">' . htmlspecialchars($_SESSION['sucesso']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    unset($_SESSION['sucesso']);
                }
                ?>
                <form action="../../index.php?action=responder-mensagem" method="POST" id="chatForm">
                    <input type="hidden" name="id_destinatario" value="<?php echo $idOutroUsuario; ?>">
                    <input type="hidden" name="assunto" value="Nova mensagem">
                    <div class="input-group">
                        <textarea name="conteudo" class="form-control" rows="2" 
                                  placeholder="Digite sua mensagem..." required 
                                  id="mensagemInput" style="resize: none;"></textarea>
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll para a última mensagem
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Auto-refresh a cada 3 segundos
        setInterval(function() {
            location.reload();
        }, 3000);

        // Enviar com Enter (Shift+Enter para nova linha)
        document.getElementById('mensagemInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('chatForm').submit();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

