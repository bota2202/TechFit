<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Login</title>
    <link rel="stylesheet" href="../Public/css/telalogin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="login-box">

        <div class="logo">
            <div class="fitness-icon">
                <i class="fas fa-dumbbell"></i>
            </div>
            <h1>TECH<span>FIT</span></h1>
        </div>
        
        <div class="welcome">
            <h3>Bem-vindo de volta!</h3>
            <p>Entre na sua conta para continuar</p>
        </div>
        
        <?php
        if (isset($_SESSION['erro'])) {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['erro']) . '</div>';
            unset($_SESSION['erro']);
        }
        if (isset($_SESSION['sucesso'])) {
            echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['sucesso']) . '</div>';
            unset($_SESSION['sucesso']);
        }
        ?>
        <form action="../../index.php?action=login" method="POST">
            <div class="mb-3">
                <input type="email" class="form-control" placeholder="E-mail" name="email" required>
            </div>
            
            <div class="mb-3">
                <input type="password" class="form-control" placeholder="Senha" name="senha" required>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="remember">
                <label class="form-check-label" for="remember">
                    Lembrar de mim
                </label>
            </div>
            
            <button type="submit" class="btn-login">Entrar</button>
            
            <div class="options">
                <a href="#" class="link-centro">Esqueci a senha</a>
            </div>
        </form>
        
        <div class="register">
            <p>É novo na TechFit? <a href="cadastro.php">Cadastre-se aqui</a></p>
            <p>Voltar para a <a href="inicial.php">Tela inicial</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

