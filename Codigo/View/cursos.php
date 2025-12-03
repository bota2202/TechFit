<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';

$usuarioLogado = Auth::isAuthenticated();
$tipoUsuario = $usuarioLogado ? ($_SESSION['usuario']['tipo'] ?? TIPO_USUARIO_ALUNO) : null;
?>
<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Cursos</title>
    <link rel="icon" type="image/svg+xml" href="../Public/favicon.svg">
    <link rel="alternate icon" href="../Public/favicon.svg">
    <link rel="stylesheet" href="../Public/css/cursos.css">
    <link rel="stylesheet" href="../Public/css/nav.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            <a class="btn-nav-centro btn-ativo" href="cursos.php">Cursos</a>
            <?php if ($usuarioLogado && $tipoUsuario == TIPO_USUARIO_ALUNO): ?>
                <a class="btn-nav-centro" href="dashboard.php">Área do Aluno</a>
            <?php elseif ($usuarioLogado && $tipoUsuario == TIPO_USUARIO_ADMIN): ?>
                <a class="btn-nav-centro" href="dashboard_admin.php">Dashboard Admin</a>
            <?php endif; ?>
        </section>

        <section class="nav-direita">
            <?php if ($usuarioLogado): ?>
                <div class="usuario-menu">
                    <div class="usuario-avatar">
                        <?php echo strtoupper(substr($_SESSION['usuario']['nome'], 0, 1)); ?>
                    </div>
                    <div class="usuario-dropdown">
                        <a href="#" class="usuario-dropdown-item">
                            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['usuario']['nome']); ?>
                        </a>
                        <a href="mensagens.php" class="usuario-dropdown-item">
                            <i class="fas fa-envelope me-2"></i>Mensagens
                        </a>
                        <a href="perfil.php" class="usuario-dropdown-item">
                            <i class="fas fa-cog me-2"></i>Configurações
                        </a>
                        <a href="../../index.php?action=logout" class="usuario-dropdown-item logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a class="btn-nav-direita btn-login" href="telalogin.php">Entrar</a>
                <a class="btn-nav-direita btn-sign" href="cadastro.php">Cadastrar-se</a>
            <?php endif; ?>
        </section>
    </nav>

    <main>
        <section class="cursos-container">
            <div class="filtros">
                <button class="filtro-btn ativo" data-tipo="todos">
                    <i class="fas fa-th"></i> Todos
                </button>
                <button class="filtro-btn" data-tipo="forca">
                    <i class="fas fa-dumbbell"></i> Força
                </button>
                <button class="filtro-btn" data-tipo="cardio">
                    <i class="fas fa-heart"></i> Cardio
                </button>
                <button class="filtro-btn" data-tipo="mente-corpo">
                    <i class="fas fa-leaf"></i> Mente-Corpo
                </button>
                <button class="filtro-btn" data-tipo="lutas">
                    <i class="fas fa-fist-raised"></i> Lutas
                </button>
            </div>

            <div class="cursos-grid" id="cursos-grid">
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-logo-section">
                <p class="footer-logo">TechFit</p>
                <p class="footer-tagline">Transformando vidas através da tecnologia e fitness</p>
            </div>
            
            <div class="footer-social">
                <a href="https://www.instagram.com/techfit.brasil/" target="_blank" rel="noopener noreferrer" class="footer-instagram">
                    <i class="fa-brands fa-instagram"></i>
                    <span>@techfit.brasil</span>
                </a>
                <a href="https://wa.me/5519999495895" target="_blank" rel="noopener noreferrer" class="footer-whatsapp">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>(19) 99949-5895</span>
                </a>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 TechFit. Todos os direitos reservados.</p>
        </div>
    </footer>

    <div class="modal-overlay" id="modal-turmas">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-titulo" id="modal-titulo">Turmas</h2>
                <button class="modal-fechar">×</button>
            </div>
            <div class="modal-body">
                <div id="modal-conteudo"></div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modal-confirmacao" style="display: none;">
        <div class="modal-container" style="max-width: 400px;">
            <div class="modal-header">
                <h2 class="modal-titulo" id="modal-confirmacao-titulo">Confirmar</h2>
                <button class="modal-fechar" onclick="fecharModalConfirmacao()">×</button>
            </div>
            <div class="modal-body">
                <p id="modal-confirmacao-texto"></p>
            </div>
            <div class="modal-footer" style="padding: 15px; border-top: 1px solid #ddd; display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn btn-secondary" onclick="fecharModalConfirmacao()" style="padding: 8px 20px; border: none; border-radius: 5px; cursor: pointer;">Cancelar</button>
                <button class="btn btn-primary" id="modal-confirmacao-confirmar" style="padding: 8px 20px; border: none; border-radius: 5px; cursor: pointer; background: #11998e; color: white;">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        let confirmacaoCallback = null;
        
        function mostrarConfirmacao(titulo, texto, callback) {
            document.getElementById('modal-confirmacao-titulo').textContent = titulo;
            document.getElementById('modal-confirmacao-texto').textContent = texto;
            confirmacaoCallback = callback;
            document.getElementById('modal-confirmacao').style.display = 'flex';
        }
        
        function fecharModalConfirmacao() {
            document.getElementById('modal-confirmacao').style.display = 'none';
            confirmacaoCallback = null;
        }
        
        document.getElementById('modal-confirmacao-confirmar').addEventListener('click', function() {
            if (confirmacaoCallback) {
                confirmacaoCallback();
                fecharModalConfirmacao();
            }
        });
        
        document.getElementById('modal-confirmacao').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalConfirmacao();
            }
        });
    </script>
    <script src="../Public/js/cursos.js"></script>
</body>
</html>

