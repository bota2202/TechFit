<?php
/**
 * Avaliações Físicas - TechFit
 */

session_start();

require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';
require_once __DIR__ . '/../Model/Conexao.php';
require_once __DIR__ . '/../Model/AvaliacaoFisicaDAO.php';
require_once __DIR__ . '/../Model/UsuarioDAO.php';

Auth::requireAuth();

$usuario = $_SESSION['usuario'];
$tipoUsuario = $usuario['tipo'] ?? TIPO_USUARIO_ALUNO;
$avaliacaoDAO = new AvaliacaoFisicaDAO();
$usuarioDAO = new UsuarioDAO();

// Busca avaliações do usuário
$avaliacoes = $avaliacaoDAO->readByUsuario($usuario['id']);
$ultimaAvaliacao = $avaliacaoDAO->readUltimaAvaliacao($usuario['id']);

// Se for admin, busca alunos para cadastrar avaliação
$podeCadastrar = Auth::isAdmin();
$alunos = [];
if ($podeCadastrar) {
    $todosUsuarios = $usuarioDAO->readAll();
    foreach ($todosUsuarios as $u) {
        if ($u->getTipo() == TIPO_USUARIO_ALUNO) {
            $alunos[] = $u;
        }
    }
}

// Verifica se precisa de nova avaliação (última avaliação há mais de 90 dias)
$precisaAvaliacao = false;
if ($ultimaAvaliacao) {
    $dataUltima = strtotime($ultimaAvaliacao->getDataAvaliacao());
    $diasDesdeUltima = (time() - $dataUltima) / (60 * 60 * 24);
    $precisaAvaliacao = $diasDesdeUltima > 90;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Avaliações Físicas</title>
    <link rel="stylesheet" href="../Public/css/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            padding-top: 80px;
            background: #f5f7fa;
        }
        .avaliacoes-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .alert-avaliacao {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
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

    <div class="avaliacoes-container">
        <?php
        if (isset($_SESSION['erro'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['erro']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['erro']);
        }
        if (isset($_SESSION['sucesso'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['sucesso']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['sucesso']);
        }

        if ($precisaAvaliacao && !$podeCadastrar):
        ?>
            <div class="alert alert-avaliacao alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle me-2"></i>Atenção!</strong> 
                Sua última avaliação física foi há mais de 90 dias. Agende uma nova avaliação com seu instrutor.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-<?php echo $podeCadastrar ? '8' : '12'; ?>">
                <?php if (!empty($avaliacoes)): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h4><i class="fas fa-chart-line me-2"></i>Evolução</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="evolucaoChart" height="100"></canvas>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h4><i class="fas fa-clipboard-list me-2"></i>Histórico de Avaliações</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Peso (kg)</th>
                                            <th>Altura (m)</th>
                                            <th>IMC</th>
                                            <th>Gordura (%)</th>
                                            <th>Massa Muscular (kg)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($avaliacoes as $avaliacao): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y', strtotime($avaliacao->getDataAvaliacao())); ?></td>
                                                <td><?php echo number_format($avaliacao->getPeso(), 2); ?></td>
                                                <td><?php echo number_format($avaliacao->getAltura(), 2); ?></td>
                                                <td><?php echo number_format($avaliacao->getImc(), 2); ?></td>
                                                <td><?php echo $avaliacao->getGorduraCorporal() ? number_format($avaliacao->getGorduraCorporal(), 2) : 'N/A'; ?></td>
                                                <td><?php echo $avaliacao->getMassaMuscular() ? number_format($avaliacao->getMassaMuscular(), 2) : 'N/A'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhuma avaliação física registrada ainda.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($podeCadastrar): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-plus-circle me-2"></i>Registrar Avaliação</h5>
                        </div>
                        <div class="card-body">
                            <form action="../../index.php?action=cadastrar-avaliacao" method="POST">
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
                                    <label class="form-label">Data da Avaliação</label>
                                    <input type="datetime-local" name="data_avaliacao" class="form-control" 
                                           value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Peso (kg)</label>
                                        <input type="number" step="0.01" name="peso" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Altura (m)</label>
                                        <input type="number" step="0.01" name="altura" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Gordura Corporal (%)</label>
                                        <input type="number" step="0.01" name="gordura_corporal" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Massa Muscular (kg)</label>
                                        <input type="number" step="0.01" name="massa_muscular" class="form-control">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Circunferência Cintura (cm)</label>
                                        <input type="number" step="0.01" name="circunferencia_cintura" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Circunferência Quadril (cm)</label>
                                        <input type="number" step="0.01" name="circunferencia_quadril" class="form-control">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observações</label>
                                    <textarea name="observacoes" class="form-control" rows="3"></textarea>
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-save me-2"></i>Salvar Avaliação
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($avaliacoes)): ?>
    <script>
        const ctx = document.getElementById('evolucaoChart').getContext('2d');
        const datas = [<?php echo implode(',', array_map(function($a) { return "'" . date('d/m/Y', strtotime($a->getDataAvaliacao())) . "'"; }, array_reverse($avaliacoes))); ?>];
        const pesos = [<?php echo implode(',', array_map(function($a) { return $a->getPeso(); }, array_reverse($avaliacoes))); ?>];
        const imcs = [<?php echo implode(',', array_map(function($a) { return $a->getImc(); }, array_reverse($avaliacoes))); ?>];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: datas,
                datasets: [{
                    label: 'Peso (kg)',
                    data: pesos,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'IMC',
                    data: imcs,
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

