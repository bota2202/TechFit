<?php
/**
 * Agendamento de Aulas - TechFit
 */

session_start();

require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';
require_once __DIR__ . '/../Model/Conexao.php';
require_once __DIR__ . '/../Model/TurmaDAO.php';
require_once __DIR__ . '/../Model/CursoDAO.php';
require_once __DIR__ . '/../Model/UsuarioTurmaDAO.php';
require_once __DIR__ . '/../Model/ListaEsperaDAO.php';

Auth::requireAuth();

$usuario = $_SESSION['usuario'];
$tipoUsuario = $usuario['tipo'] ?? TIPO_USUARIO_ALUNO;
$turmaDAO = new TurmaDAO();
$cursoDAO = new CursoDAO();
$usuarioTurmaDAO = new UsuarioTurmaDAO();
$listaEsperaDAO = new ListaEsperaDAO();

// Busca todas as turmas disponíveis
$todasTurmas = $turmaDAO->readAll();
$cursos = $cursoDAO->readAll();

// Busca turmas do usuário
$minhasTurmas = $usuarioTurmaDAO->readByUsuario($usuario['id']);
$minhasListaEspera = $listaEsperaDAO->readByUsuario($usuario['id']);

// Organiza turmas por curso
$turmasPorCurso = [];
foreach ($todasTurmas as $turma) {
    $cursoId = $turma->getIdCurso();
    if (!isset($turmasPorCurso[$cursoId])) {
        $turmasPorCurso[$cursoId] = [];
    }
    $turmasPorCurso[$cursoId][] = $turma;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Agendamento de Aulas</title>
    <link rel="stylesheet" href="../Public/css/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding-top: 80px;
            background: #f5f7fa;
        }
        .agendamento-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .turma-card {
            border-left: 4px solid #11998e;
            transition: transform 0.2s;
        }
        .turma-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .btn-agendar {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
        }
        .btn-cancelar {
            background: #dc3545;
            border: none;
            color: white;
        }
        .badge-lotada {
            background: #ffc107;
            color: #000;
        }
        .badge-disponivel {
            background: #28a745;
            color: white;
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

    <div class="agendamento-container">
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
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-calendar-alt me-2"></i>Turmas Disponíveis</h4>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cursos as $curso): ?>
                            <?php if (isset($turmasPorCurso[$curso->getId()])): ?>
                                <h5 class="mt-3 mb-3"><?php echo htmlspecialchars($curso->getNome()); ?></h5>
                                <?php foreach ($turmasPorCurso[$curso->getId()] as $turma): ?>
                                    <?php
                                    $matriculados = $usuarioTurmaDAO->readByTurma($turma->getId());
                                    $capacidade = $turma->getCapacidadeMaxima() ?: 20;
                                    $ocupacao = count($matriculados);
                                    $disponivel = $ocupacao < $capacidade;
                                    $jaMatriculado = false;
                                    foreach ($minhasTurmas as $minhaTurma) {
                                        if ($minhaTurma->getIdTurma() == $turma->getId()) {
                                            $jaMatriculado = true;
                                            break;
                                        }
                                    }
                                    $naListaEspera = false;
                                    foreach ($minhasListaEspera as $espera) {
                                        if ($espera->getIdTurma() == $turma->getId()) {
                                            $naListaEspera = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="card turma-card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5><?php echo htmlspecialchars($turma->getNome()); ?></h5>
                                                    <p class="mb-1"><i class="fas fa-clock me-2"></i><?php echo htmlspecialchars($turma->getHorario()); ?></p>
                                                    <p class="mb-1"><i class="fas fa-calendar me-2"></i>
                                                        <?php echo date('d/m/Y', strtotime($turma->getDataInicio())); ?> - 
                                                        <?php echo date('d/m/Y', strtotime($turma->getDataFim())); ?>
                                                    </p>
                                                    <p class="mb-0">
                                                        <span class="badge <?php echo $disponivel ? 'badge-disponivel' : 'badge-lotada'; ?>">
                                                            <?php echo $ocupacao; ?>/<?php echo $capacidade; ?> vagas
                                                        </span>
                                                    </p>
                                                </div>
                                                <div>
                                                    <?php if ($jaMatriculado): ?>
                                                        <form action="../../index.php?action=cancelar-agendamento" method="POST" style="display:inline;">
                                                            <input type="hidden" name="id_turma" value="<?php echo $turma->getId(); ?>">
                                                            <button type="submit" class="btn btn-cancelar btn-sm">
                                                                <i class="fas fa-times me-1"></i>Cancelar
                                                            </button>
                                                        </form>
                                                    <?php elseif ($naListaEspera): ?>
                                                        <span class="badge bg-warning text-dark">Na Lista de Espera</span>
                                                    <?php elseif ($disponivel): ?>
                                                        <form action="../../index.php?action=agendar" method="POST" style="display:inline;">
                                                            <input type="hidden" name="id_turma" value="<?php echo $turma->getId(); ?>">
                                                            <button type="submit" class="btn btn-agendar btn-sm">
                                                                <i class="fas fa-check me-1"></i>Agendar
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form action="../../index.php?action=agendar" method="POST" style="display:inline;">
                                                            <input type="hidden" name="id_turma" value="<?php echo $turma->getId(); ?>">
                                                            <button type="submit" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-clock me-1"></i>Lista de Espera
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-list me-2"></i>Minhas Turmas</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($minhasTurmas)): ?>
                            <?php foreach ($minhasTurmas as $matricula): ?>
                                <?php $turma = $turmaDAO->readById($matricula->getIdTurma()); ?>
                                <?php if ($turma): ?>
                                    <div class="mb-3 p-2 border rounded">
                                        <strong><?php echo htmlspecialchars($turma->getNome()); ?></strong><br>
                                        <small><?php echo htmlspecialchars($turma->getHorario()); ?></small>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Você não está matriculado em nenhuma turma.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-warning text-dark">
                        <h5><i class="fas fa-hourglass-half me-2"></i>Lista de Espera</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($minhasListaEspera)): ?>
                            <?php foreach ($minhasListaEspera as $espera): ?>
                                <?php $turma = $turmaDAO->readById($espera->getIdTurma()); ?>
                                <?php if ($turma): ?>
                                    <div class="mb-3 p-2 border rounded">
                                        <strong><?php echo htmlspecialchars($turma->getNome()); ?></strong><br>
                                        <small>Aguardando vaga disponível</small>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Você não está em nenhuma lista de espera.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

