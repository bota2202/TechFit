<?php
/**
 * Dashboard do Admin - TechFit
 * Área restrita para administradores com abas e CRUDs completos
 */

session_start();

require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';
require_once __DIR__ . '/../Model/Conexao.php';
require_once __DIR__ . '/../Model/UsuarioDAO.php';
require_once __DIR__ . '/../Model/TreinoDAO.php';
require_once __DIR__ . '/../Model/DietaDAO.php';
require_once __DIR__ . '/../Model/TurmaDAO.php';
require_once __DIR__ . '/../Model/CursoDAO.php';
require_once __DIR__ . '/../Model/UnidadeDAO.php';
require_once __DIR__ . '/../Model/PlanoDAO.php';
require_once __DIR__ . '/../Model/UsuarioTurmaDAO.php';
require_once __DIR__ . '/../Model/AvaliacaoFisicaDAO.php';
require_once __DIR__ . '/../Model/MensagemDAO.php';
require_once __DIR__ . '/../Model/helpers.php';

// Verifica se o usuário é admin
Auth::requireAdmin();

$usuario = $_SESSION['usuario'];
$usuarioDAO = new UsuarioDAO();
$treinoDAO = new TreinoDAO();
$dietaDAO = new DietaDAO();
$turmaDAO = new TurmaDAO();
$cursoDAO = new CursoDAO();
$unidadeDAO = new UnidadeDAO();
$planoDAO = new PlanoDAO();
$usuarioTurmaDAO = new UsuarioTurmaDAO();
$avaliacaoDAO = new AvaliacaoFisicaDAO();
$mensagemDAO = new MensagemDAO();

// Busca todos os dados
$usuarios = $usuarioDAO->readAll();
$alunos = [];
$instrutores = [];
foreach ($usuarios as $u) {
    if ($u->getTipo() == TIPO_USUARIO_ALUNO) {
        $alunos[] = $u;
    } elseif ($u->getTipo() == TIPO_USUARIO_INSTRUTOR) {
        $instrutores[] = $u;
    }
}

$unidades = $unidadeDAO->readAll();
$cursos = $cursoDAO->readAll();
$planos = $planoDAO->readAll();
$turmas = $turmaDAO->readAll();

// Estatísticas
$totalAlunos = count($alunos);
$totalTurmas = count($turmas);
$totalCursos = count($cursos);
$totalUnidades = count($unidades);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Dashboard Admin</title>
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }
        .stat-card {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #11998e;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        .nav-tabs .nav-link {
            color: #666;
            border: none;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: #11998e;
            border-bottom: 2px solid #11998e;
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
                    <a href="dashboard_admin.php" class="usuario-dropdown-item">Dashboard Admin</a>
                    <a href="../../index.php?action=logout" class="usuario-dropdown-item logout">
                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                    </a>
                </div>
            </div>
        </section>
    </nav>

    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-user-shield me-2"></i>Dashboard Admin</h1>
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

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalAlunos; ?></div>
                    <div class="stat-label">Total de Alunos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalTurmas; ?></div>
                    <div class="stat-label">Turmas Ativas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalCursos; ?></div>
                    <div class="stat-label">Cursos Disponíveis</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalUnidades; ?></div>
                    <div class="stat-label">Unidades</div>
                </div>
            </div>
        </div>

        <!-- Abas de Gestão -->
        <ul class="nav nav-tabs mb-3" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="unidades-tab" data-bs-toggle="tab" data-bs-target="#unidades" type="button" role="tab">
                    <i class="fas fa-building me-2"></i>Unidades
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cursos-tab" data-bs-toggle="tab" data-bs-target="#cursos" type="button" role="tab">
                    <i class="fas fa-book me-2"></i>Cursos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="planos-tab" data-bs-toggle="tab" data-bs-target="#planos" type="button" role="tab">
                    <i class="fas fa-credit-card me-2"></i>Planos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="turmas-tab" data-bs-toggle="tab" data-bs-target="#turmas" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>Turmas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="alunos-tab" data-bs-toggle="tab" data-bs-target="#alunos" type="button" role="tab">
                    <i class="fas fa-user-graduate me-2"></i>Alunos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="treinos-tab" data-bs-toggle="tab" data-bs-target="#treinos" type="button" role="tab">
                    <i class="fas fa-dumbbell me-2"></i>Treinos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dietas-tab" data-bs-toggle="tab" data-bs-target="#dietas" type="button" role="tab">
                    <i class="fas fa-utensils me-2"></i>Dietas
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabContent">
            <!-- Aba Unidades -->
            <div class="tab-pane fade show active" id="unidades" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Unidades</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalUnidade">
                        <i class="fas fa-plus me-2"></i>Nova Unidade
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Bairro</th>
                                <th>Rua</th>
                                <th>Número</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unidades as $unidade): ?>
                                <tr>
                                    <td><?php echo $unidade->getId(); ?></td>
                                    <td><?php echo htmlspecialchars($unidade->getEstado()); ?></td>
                                    <td><?php echo htmlspecialchars($unidade->getCidade()); ?></td>
                                    <td><?php echo htmlspecialchars($unidade->getBairro()); ?></td>
                                    <td><?php echo htmlspecialchars($unidade->getRua()); ?></td>
                                    <td><?php echo $unidade->getNumero(); ?></td>
                                    <td class="table-actions">
                                        <button class="btn btn-sm btn-warning" onclick="editarUnidade(<?php echo $unidade->getId(); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="../../index.php?action=unidade-deletar" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar esta unidade?');">
                                            <input type="hidden" name="id" value="<?php echo $unidade->getId(); ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Cursos -->
            <div class="tab-pane fade" id="cursos" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Cursos</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalCurso">
                        <i class="fas fa-plus me-2"></i>Novo Curso
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Preço</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $curso): ?>
                                <tr>
                                    <td><?php echo $curso->getId(); ?></td>
                                    <td><?php echo htmlspecialchars($curso->getNome()); ?></td>
                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($curso->getTipo()); ?></span></td>
                                    <td><?php echo htmlspecialchars(substr($curso->getDescricao(), 0, 50)); ?>...</td>
                                    <td>R$ <?php echo number_format($curso->getPreco(), 2, ',', '.'); ?></td>
                                    <td class="table-actions">
                                        <button class="btn btn-sm btn-warning" onclick="editarCurso(<?php echo $curso->getId(); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="../../index.php?action=curso-deletar" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar este curso?');">
                                            <input type="hidden" name="id" value="<?php echo $curso->getId(); ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Planos -->
            <div class="tab-pane fade" id="planos" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Planos</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalPlano">
                        <i class="fas fa-plus me-2"></i>Novo Plano
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Preço</th>
                                <th>Descrição</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($planos as $plano): ?>
                                <tr>
                                    <td><?php echo $plano->getId(); ?></td>
                                    <td>R$ <?php echo number_format($plano->getPreco(), 2, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($plano->getDescricao()); ?></td>
                                    <td class="table-actions">
                                        <button class="btn btn-sm btn-warning" onclick="editarPlano(<?php echo $plano->getId(); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="../../index.php?action=plano-deletar" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar este plano?');">
                                            <input type="hidden" name="id" value="<?php echo $plano->getId(); ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Turmas -->
            <div class="tab-pane fade" id="turmas" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gerenciar Turmas</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTurma">
                        <i class="fas fa-plus me-2"></i>Nova Turma
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Curso</th>
                                <th>Horário</th>
                                <th>Data Início</th>
                                <th>Data Fim</th>
                                <th>Capacidade</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($turmas as $turma): ?>
                                <?php 
                                $curso = $cursoDAO->readById($turma->getIdCurso());
                                $matriculados = $usuarioTurmaDAO->readByTurma($turma->getId());
                                ?>
                                <tr>
                                    <td><?php echo $turma->getId(); ?></td>
                                    <td><?php echo htmlspecialchars($turma->getNome()); ?></td>
                                    <td><?php echo $curso ? htmlspecialchars($curso->getNome()) : 'N/A'; ?></td>
                                    <td><?php echo htmlspecialchars($turma->getHorario()); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($turma->getDataInicio())); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($turma->getDataFim())); ?></td>
                                    <td><?php echo count($matriculados); ?>/<?php echo $turma->getCapacidadeMaxima() ?: 20; ?></td>
                                    <td class="table-actions">
                                        <button class="btn btn-sm btn-info" onclick="verTurma(<?php echo $turma->getId(); ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editarTurma(<?php echo $turma->getId(); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="../../index.php?action=turma-deletar" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar esta turma?');">
                                            <input type="hidden" name="id" value="<?php echo $turma->getId(); ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Alunos -->
            <div class="tab-pane fade" id="alunos" role="tabpanel">
                <h4>Gerenciar Alunos</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>CPF</th>
                                <th>Cidade</th>
                                <th>Telefone</th>
                                <th class="table-actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alunos as $aluno): ?>
                                <tr>
                                    <td><?php echo $aluno->getId(); ?></td>
                                    <td><?php echo htmlspecialchars($aluno->getNome()); ?></td>
                                    <td><?php echo htmlspecialchars($aluno->getEmail()); ?></td>
                                    <td><?php echo htmlspecialchars($aluno->getCpf()); ?></td>
                                    <td><?php echo htmlspecialchars($aluno->getCidade()); ?></td>
                                    <td><?php echo htmlspecialchars($aluno->getTelefone()); ?></td>
                                    <td class="table-actions">
                                        <a href="avaliacoes.php?usuario=<?php echo $aluno->getId(); ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-clipboard-list"></i> Avaliações
                                        </a>
                                        <a href="chat.php?usuario=<?php echo $aluno->getId(); ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-comments"></i> Mensagem
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba Treinos -->
            <div class="tab-pane fade" id="treinos" role="tabpanel">
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
                            <?php 
                            $todosTreinos = [];
                            foreach ($alunos as $aluno) {
                                $treinosAluno = $treinoDAO->readByUsuarioId($aluno->getId());
                                foreach ($treinosAluno as $treino) {
                                    $todosTreinos[] = ['treino' => $treino, 'aluno' => $aluno];
                                }
                            }
                            ?>
                            <?php if (!empty($todosTreinos)): ?>
                                <?php foreach ($todosTreinos as $item): ?>
                                    <tr>
                                        <td><?php echo $item['treino']['id']; ?></td>
                                        <td><?php echo htmlspecialchars($item['aluno']->getNome()); ?></td>
                                        <td><?php echo htmlspecialchars($item['treino']['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($item['treino']['descricao'], 0, 50)); ?>...</td>
                                        <td><?php echo date('d/m/Y', strtotime($item['treino']['data_criacao'])); ?></td>
                                        <td class="table-actions">
                                            <form action="../../index.php?action=treino-deletar" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar este treino?');">
                                                <input type="hidden" name="id" value="<?php echo $item['treino']['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
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
                            <?php 
                            $todasDietas = [];
                            foreach ($alunos as $aluno) {
                                $dietasAluno = $dietaDAO->readByUsuarioId($aluno->getId());
                                foreach ($dietasAluno as $dieta) {
                                    $todasDietas[] = ['dieta' => $dieta, 'aluno' => $aluno];
                                }
                            }
                            ?>
                            <?php if (!empty($todasDietas)): ?>
                                <?php foreach ($todasDietas as $item): ?>
                                    <tr>
                                        <td><?php echo $item['dieta']['id']; ?></td>
                                        <td><?php echo htmlspecialchars($item['aluno']->getNome()); ?></td>
                                        <td><?php echo htmlspecialchars($item['dieta']['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($item['dieta']['descricao'], 0, 50)); ?>...</td>
                                        <td><?php echo date('d/m/Y', strtotime($item['dieta']['data_criacao'])); ?></td>
                                        <td class="table-actions">
                                            <form action="../../index.php?action=dieta-deletar" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar esta dieta?');">
                                                <input type="hidden" name="id" value="<?php echo $item['dieta']['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
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
        </div>
    </div>

    <!-- Modal Unidade -->
    <div class="modal fade" id="modalUnidade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Unidade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../index.php?action=unidade-cadastrar" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <input type="text" name="estado" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="cidade" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bairro</label>
                            <input type="text" name="bairro" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rua</label>
                            <input type="text" name="rua" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Número</label>
                            <input type="number" name="numero" class="form-control" required>
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

    <!-- Modal Curso -->
    <div class="modal fade" id="modalCurso" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../index.php?action=curso-cadastrar" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="forca">Força</option>
                                <option value="cardio">Cardio</option>
                                <option value="mente-corpo">Mente-Corpo</option>
                                <option value="lutas">Lutas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preço</label>
                            <input type="number" step="0.01" name="preco" class="form-control" required>
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

    <!-- Modal Plano -->
    <div class="modal fade" id="modalPlano" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Plano</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../index.php?action=plano-cadastrar" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Preço</label>
                            <input type="number" step="0.01" name="preco" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="3" required></textarea>
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

    <!-- Modal Turma -->
    <div class="modal fade" id="modalTurma" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Turma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../index.php?action=turma-cadastrar" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Curso</label>
                            <select name="id_curso" class="form-select" required>
                                <option value="">Selecione um curso</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?php echo $curso->getId(); ?>">
                                        <?php echo htmlspecialchars($curso->getNome()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Responsável</label>
                            <select name="responsavel" class="form-select" required>
                                <option value="">Selecione um instrutor</option>
                                <?php foreach ($instrutores as $instrutor): ?>
                                    <option value="<?php echo $instrutor->getId(); ?>">
                                        <?php echo htmlspecialchars($instrutor->getNome()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nome da Turma</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Horário</label>
                            <input type="text" name="horario" class="form-control" placeholder="Ex: Segunda e Quarta, 18:00" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data Início</label>
                                <input type="datetime-local" name="data_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data Fim</label>
                                <input type="datetime-local" name="data_fim" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacidade Máxima</label>
                            <input type="number" name="capacidade_maxima" class="form-control" value="20" min="1" required>
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

    <!-- Modal Treino -->
    <div class="modal fade" id="modalTreino" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Treino</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../index.php?action=treino-cadastrar" method="POST">
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
                <form action="../../index.php?action=dieta-cadastrar" method="POST">
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

    <script>
        function editarUnidade(id) {
            // Implementar edição
            alert('Funcionalidade de edição em desenvolvimento');
        }
        function editarCurso(id) {
            alert('Funcionalidade de edição em desenvolvimento');
        }
        function editarPlano(id) {
            alert('Funcionalidade de edição em desenvolvimento');
        }
        function editarTurma(id) {
            alert('Funcionalidade de edição em desenvolvimento');
        }
        function verTurma(id) {
            window.location.href = 'cursos.php?turma=' + id;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
