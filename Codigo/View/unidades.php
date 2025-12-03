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
    <title>TechFit - Unidades</title>
    <link rel="icon" type="image/svg+xml" href="../Public/favicon.svg">
    <link rel="alternate icon" href="../Public/favicon.svg">
    <link rel="stylesheet" href="../Public/css/unidades.css">
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
            <a class="btn-nav-centro btn-ativo" href="unidades.php">Unidades</a>
            <a class="btn-nav-centro" href="cursos.php">Cursos</a>
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
        <div class="lista-unidades" id="lista-unidades">
        </div>
        
        <div class="detalhes-unidade vazio" id="detalhes-unidade">
        </div>
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

    <script src="../Public/js/unidades.js"></script>
</body>
</html>

