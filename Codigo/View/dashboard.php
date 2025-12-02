<?php
/**
 * Dashboard do Aluno - TechFit
 * Área restrita para usuários logados
 */

session_start();

require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';
require_once __DIR__ . '/../Model/Conexao.php';
require_once __DIR__ . '/../Model/TreinoDAO.php';
require_once __DIR__ . '/../Model/DietaDAO.php';

// Verifica se o usuário está logado
Auth::requireAuth();

$usuario = $_SESSION['usuario'];
$treinoDAO = new TreinoDAO();
$dietaDAO = new DietaDAO();

// Busca treinos e dietas do usuário
$treinos = $treinoDAO->readByUsuarioId($usuario['id']);
$dietas = $dietaDAO->readByUsuarioId($usuario['id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Área do Aluno</title>
    <link rel="stylesheet" href="../Public/css/aluno.css">
    <link rel="stylesheet" href="../Public/css/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding-top: 80px !important;
        }
        nav {
            z-index: 1000;
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
            <a class="btn-nav-centro btn-ativo" href="dashboard.php">Área do Aluno</a>
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

    <div class="aluno-container" style="margin-top: 80px;">
        <div class="header">
            <div class="logo">
                <h1>TECH<span>FIT</span></h1>
                <div class="fitness-icon">
                    <i class="fas fa-dumbbell" style="font-size: 40px; color: var(--verde-principal);"></i>
                </div>
            </div>
            
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($usuario['nome']); ?></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <?php if (!empty($treinos)): ?>
                    <div class="card">
                        <h3 class="card-title">Meu Plano de Treino</h3>
                        <?php foreach ($treinos as $treino): ?>
                            <div class="workout-day">
                                <div class="day-title"><?php echo htmlspecialchars($treino['titulo']); ?></div>
                                <div class="workout-content">
                                    <?php echo nl2br(htmlspecialchars($treino['descricao'])); ?>
                                </div>
                                <?php if (!empty($treino['observacoes'])): ?>
                                    <div class="workout-notes">
                                        <strong>Observações:</strong> <?php echo nl2br(htmlspecialchars($treino['observacoes'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <h3 class="card-title">Meu Plano de Treino</h3>
                        <p class="text-muted">Nenhum treino foi atribuído ainda. Entre em contato com seu instrutor.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-3">
                    <h3 class="card-title">Ações Rápidas</h3>
                    <div class="card-body">
                        <a href="agendamento.php" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-calendar-check me-2"></i>Agendar Aula
                        </a>
                        <a href="mensagens.php" class="btn btn-info w-100 mb-2">
                            <i class="fas fa-envelope me-2"></i>Mensagens
                        </a>
                        <a href="avaliacoes.php" class="btn btn-warning w-100">
                            <i class="fas fa-clipboard-list me-2"></i>Avaliações Físicas
                        </a>
                    </div>
                </div>

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="nutrition-tab" data-bs-toggle="tab" data-bs-target="#nutrition" type="button" role="tab">
                            <i class="fas fa-utensils me-2"></i>Nutrição
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="nutrition" role="tabpanel">
                        <div class="card">
                            <h3 class="card-title">Plano Alimentar</h3>
                            <?php if (!empty($dietas)): ?>
                                <?php foreach ($dietas as $dieta): ?>
                                    <div class="meal-plan">
                                        <div class="meal-title"><?php echo htmlspecialchars($dieta['titulo']); ?></div>
                                        <div class="meal-items">
                                            <?php echo nl2br(htmlspecialchars($dieta['descricao'])); ?>
                                        </div>
                                        <?php if (!empty($dieta['observacoes'])): ?>
                                            <div class="meal-notes">
                                                <strong>Observações:</strong> <?php echo nl2br(htmlspecialchars($dieta['observacoes'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">Nenhum plano alimentar foi atribuído ainda. Entre em contato com seu nutricionista.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>TechFit Academia &copy; 2024 - Transforme seu corpo, transforme sua vida</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
