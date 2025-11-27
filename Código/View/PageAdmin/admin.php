<?php
session_start();
require_once '../DB/conexao.php';
require_once '../DB/verificar_sessao.php';

// Verificar se é admin (id = 1)
if (!verificarAdmin()) {
    header('Location: ../PageLogin/telalogin.html');
    exit;
}

$dados_usuario = obterDadosUsuario();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Painel Administrativo</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <div class="logo">
                <h1>TECH<span>FIT</span></h1>
                <div class="fitness-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
            </div>
            
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($dados_usuario['nome_usuario']); ?></div>
                <div class="user-plan">Administrador</div>
                <a href="../DB/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab">
                            <i class="fas fa-users"></i> Usuários
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="planos-tab" data-bs-toggle="tab" data-bs-target="#planos" type="button" role="tab">
                            <i class="fas fa-credit-card"></i> Planos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cursos-tab" data-bs-toggle="tab" data-bs-target="#cursos" type="button" role="tab">
                            <i class="fas fa-book"></i> Cursos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="turmas-tab" data-bs-toggle="tab" data-bs-target="#turmas" type="button" role="tab">
                            <i class="fas fa-chalkboard-teacher"></i> Turmas
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="unidades-tab" data-bs-toggle="tab" data-bs-target="#unidades" type="button" role="tab">
                            <i class="fas fa-building"></i> Unidades
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pagamentos-tab" data-bs-toggle="tab" data-bs-target="#pagamentos" type="button" role="tab">
                            <i class="fas fa-money-bill-wave"></i> Pagamentos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="presencas-tab" data-bs-toggle="tab" data-bs-target="#presencas" type="button" role="tab">
                            <i class="fas fa-check-circle"></i> Presenças
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Dashboard -->
                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                        <div class="card">
                            <h3 class="card-title">Dashboard</h3>
                            <div class="stats-grid" id="dashboard-stats">
                                <div class="stat-item">
                                    <div class="stat-value" id="total-usuarios">-</div>
                                    <div class="stat-label">Total de Usuários</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="total-planos">-</div>
                                    <div class="stat-label">Planos Ativos</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="total-cursos">-</div>
                                    <div class="stat-label">Cursos Disponíveis</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="total-turmas">-</div>
                                    <div class="stat-label">Turmas Ativas</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="total-unidades">-</div>
                                    <div class="stat-label">Unidades</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="total-pagamentos">-</div>
                                    <div class="stat-label">Pagamentos (Mês)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usuários -->
                    <div class="tab-pane fade" id="usuarios" role="tabpanel">
                        <div class="card">
                            <div class="card-header-actions">
                                <h3 class="card-title">Usuários</h3>
                                <button class="btn-primary-custom" onclick="abrirModalUsuario()">
                                    <i class="fas fa-plus"></i> Novo Usuário
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table-admin">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>CPF</th>
                                            <th>Tipo</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usuarios-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Planos -->
                    <div class="tab-pane fade" id="planos" role="tabpanel">
                        <div class="card">
                            <div class="card-header-actions">
                                <h3 class="card-title">Planos</h3>
                                <button class="btn-primary-custom" onclick="abrirModalPlano()">
                                    <i class="fas fa-plus"></i> Novo Plano
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table-admin">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Preço</th>
                                            <th>Descrição</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="planos-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Cursos -->
                    <div class="tab-pane fade" id="cursos" role="tabpanel">
                        <div class="card">
                            <div class="card-header-actions">
                                <h3 class="card-title">Cursos</h3>
                                <button class="btn-primary-custom" onclick="abrirModalCurso()">
                                    <i class="fas fa-plus"></i> Novo Curso
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table-admin">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Tipo</th>
                                            <th>Preço</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cursos-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Turmas -->
                    <div class="tab-pane fade" id="turmas" role="tabpanel">
                        <div class="card">
                            <div class="card-header-actions">
                                <h3 class="card-title">Turmas</h3>
                                <button class="btn-primary-custom" onclick="abrirModalTurma()">
                                    <i class="fas fa-plus"></i> Nova Turma
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table-admin">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Curso</th>
                                            <th>Responsável</th>
                                            <th>Horário</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="turmas-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Unidades -->
                    <div class="tab-pane fade" id="unidades" role="tabpanel">
                        <div class="card">
                            <div class="card-header-actions">
                                <h3 class="card-title">Unidades</h3>
                                <button class="btn-primary-custom" onclick="abrirModalUnidade()">
                                    <i class="fas fa-plus"></i> Nova Unidade
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table-admin">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cidade</th>
                                            <th>Estado</th>
                                            <th>Bairro</th>
                                            <th>Rua</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="unidades-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Pagamentos -->
                    <div class="tab-pane fade" id="pagamentos" role="tabpanel">
                        <div class="card">
                            <div class="card-header-actions">
                                <h3 class="card-title">Pagamentos</h3>
                                <button class="btn-primary-custom" onclick="abrirModalPagamento()">
                                    <i class="fas fa-plus"></i> Novo Pagamento
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table-admin">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Usuário</th>
                                            <th>Plano</th>
                                            <th>Valor</th>
                                            <th>Data</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pagamentos-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Presenças -->
                    <div class="tab-pane fade" id="presencas" role="tabpanel">
                        <div class="card">
                            <div class="card-header-actions">
                                <h3 class="card-title">Presenças</h3>
                                <button class="btn-primary-custom" onclick="abrirModalPresenca()">
                                    <i class="fas fa-plus"></i> Nova Presença
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table-admin">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Usuário</th>
                                            <th>Turma</th>
                                            <th>Data</th>
                                            <th>Presente</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="presencas-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modais serão inseridos via JavaScript -->
    <div id="modal-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>
</body>
</html>

