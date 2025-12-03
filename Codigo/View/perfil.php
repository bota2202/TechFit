<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/../Model/Auth.php';
require_once __DIR__ . '/../Model/helpers.php';
require_once __DIR__ . '/../Model/UsuarioDAO.php';
require_once __DIR__ . '/../Model/UsuarioPlanoDAO.php';
require_once __DIR__ . '/../Model/PlanoDAO.php';

Auth::requireAuth();

$usuarioDAO = new UsuarioDAO();
$usuarioPlanoDAO = new UsuarioPlanoDAO();
$planoDAO = new PlanoDAO();

$usuario = $usuarioDAO->readById($_SESSION['usuario']['id']);
$temPlanoAtivo = $usuarioPlanoDAO->temPlanoAtivo($_SESSION['usuario']['id']);
$planoAtivo = null;
if ($temPlanoAtivo) {
    $planoAtivo = $usuarioPlanoDAO->getPlanoAtivo($_SESSION['usuario']['id']);
}

$todosPlanos = $planoDAO->readAll();
$planosDisponiveis = [];
foreach ($todosPlanos as $plano) {
    if (!$temPlanoAtivo || $plano->getId() != $planoAtivo['id_plano']) {
        $planosDisponiveis[] = $plano;
    }
}

$tipoUsuario = $_SESSION['usuario']['tipo'] ?? TIPO_USUARIO_ALUNO;
?>
<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Meu Perfil</title>
    <link rel="icon" type="image/svg+xml" href="../Public/favicon.svg">
    <link rel="alternate icon" href="../Public/favicon.svg">
    <link rel="stylesheet" href="../Public/css/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding-top: 80px;
            background: #f5f5f5;
        }
        .perfil-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .perfil-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .avatar-grande {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 20px;
            font-weight: bold;
        }
        .perfil-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .perfil-card h3 {
            color: #11998e;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .btn-editar-perfil {
            background: #11998e;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-editar-perfil:hover {
            background: #0d7a6f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
        }
        .plano-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #d4edda;
            color: #155724;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 10px;
        }
        .planos-melhorar {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .plano-melhorar-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .plano-melhorar-item:hover {
            border-color: #11998e;
            box-shadow: 0 2px 8px rgba(17, 153, 142, 0.2);
        }
        .plano-melhorar-info h5 {
            color: #333;
            margin-bottom: 5px;
            font-size: 1rem;
        }
        .plano-melhorar-preco {
            color: #11998e;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 0;
        }
        .btn-melhorar-plano {
            background: #11998e;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s;
            cursor: pointer;
        }
        .btn-melhorar-plano:hover {
            background: #0d7a6f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
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
                <a class="btn-nav-centro" href="dashboard.php">Área do Aluno</a>
            <?php elseif ($tipoUsuario == TIPO_USUARIO_ADMIN): ?>
                <a class="btn-nav-centro" href="dashboard_admin.php">Dashboard Admin</a>
            <?php elseif ($tipoUsuario == TIPO_USUARIO_INSTRUTOR): ?>
                <a class="btn-nav-centro" href="dashboard_instrutor.php">Dashboard Instrutor</a>
            <?php endif; ?>
        </section>

        <section class="nav-direita">
            <div class="usuario-menu">
                <div class="usuario-avatar">
                    <?php echo strtoupper(substr($usuario->getNome(), 0, 1)); ?>
                </div>
                <div class="usuario-dropdown">
                    <a href="perfil.php" class="usuario-dropdown-item">
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($usuario->getNome()); ?>
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

    <div class="perfil-container">
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

        <div class="perfil-header">
            <div class="avatar-grande">
                <?php echo strtoupper(substr($usuario->getNome(), 0, 1)); ?>
            </div>
            <h1><?php echo htmlspecialchars($usuario->getNome()); ?></h1>
            <?php if ($temPlanoAtivo && $planoAtivo): ?>
                <div class="plano-badge">
                    <i class="fas fa-check-circle me-2"></i>Plano Ativo
                </div>
            <?php else: ?>
                <p class="text-muted">Sem plano ativo</p>
            <?php endif; ?>
        </div>

        <div class="perfil-card">
            <h3><i class="fas fa-user me-2"></i>Informações Pessoais</h3>
            <div class="info-item">
                <span class="info-label">Nome:</span>
                <span class="info-value"><?php echo htmlspecialchars($usuario->getNome()); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($usuario->getEmail()); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">CPF:</span>
                <span class="info-value"><?php echo htmlspecialchars($usuario->getCpf()); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Telefone:</span>
                <span class="info-value"><?php echo htmlspecialchars($usuario->getTelefone()); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Tipo de Usuário:</span>
                <span class="info-value">
                    <?php 
                    if ($tipoUsuario == TIPO_USUARIO_ADMIN) echo 'Administrador';
                    elseif ($tipoUsuario == TIPO_USUARIO_INSTRUTOR) echo 'Instrutor';
                    else echo 'Aluno';
                    ?>
                </span>
            </div>
        </div>

        <div class="perfil-card">
            <h3><i class="fas fa-map-marker-alt me-2"></i>Endereço</h3>
            <div class="info-item">
                <span class="info-label">Estado:</span>
                <span class="info-value"><?php echo htmlspecialchars($usuario->getEstado() ?? 'Não informado'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Cidade:</span>
                <span class="info-value"><?php echo htmlspecialchars($usuario->getCidade() ?? 'Não informado'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Bairro:</span>
                <span class="info-value"><?php echo htmlspecialchars($usuario->getBairro() ?? 'Não informado'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Rua:</span>
                <span class="info-value"><?php echo htmlspecialchars($usuario->getRua() ?? 'Não informado'); ?></span>
            </div>
        </div>

        <?php if ($temPlanoAtivo && $planoAtivo): ?>
        <div class="perfil-card">
            <h3><i class="fas fa-credit-card me-2"></i>Plano Ativo</h3>
            <div class="info-item">
                <span class="info-label">Descrição:</span>
                <span class="info-value"><?php echo htmlspecialchars($planoAtivo['descricao_plano'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Preço:</span>
                <span class="info-value">R$ <?php echo number_format($planoAtivo['preco_plano'] ?? 0, 2, ',', '.'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Data de Início:</span>
                <span class="info-value"><?php echo date('d/m/Y', strtotime($planoAtivo['data_inicio_plano'])); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Data de Término:</span>
                <span class="info-value"><?php echo date('d/m/Y', strtotime($planoAtivo['data_fim_plano'])); ?></span>
            </div>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
                <form action="<?php echo getActionUrl('cancelar-plano'); ?>" method="POST" style="display: inline;" id="formCancelarPlano">
                    <button type="button" class="btn btn-danger" onclick="confirmarCancelarPlano();" style="padding: 10px 20px; border-radius: 5px; border: none; cursor: pointer;">
                        <i class="fas fa-times-circle me-2"></i>Cancelar Plano
                    </button>
                </form>
            </div>
            <?php if (!empty($planosDisponiveis)): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
                <h4 style="color: #11998e; margin-bottom: 15px;"><i class="fas fa-exchange-alt me-2"></i>Trocar Plano</h4>
                <p style="color: #666; margin-bottom: 15px;">Escolha outro plano para substituir o atual:</p>
                <div class="planos-melhorar">
                    <?php 
                    $precoAtual = $planoAtivo['preco_plano'] ?? 0;
                    foreach ($planosDisponiveis as $plano): 
                        $precoNovo = $plano->getPreco();
                        $ehMelhor = $precoNovo > $precoAtual;
                        $ehPior = $precoNovo < $precoAtual;
                        $ehIgual = $precoNovo == $precoAtual;
                    ?>
                        <div class="plano-melhorar-item">
                            <div class="plano-melhorar-info">
                                <h5><?php echo htmlspecialchars($plano->getDescricao()); ?></h5>
                                <p class="plano-melhorar-preco">R$ <?php echo number_format($precoNovo, 2, ',', '.'); ?>/mês</p>
                                <?php if ($ehMelhor): ?>
                                    <small style="color: #28a745;"><i class="fas fa-arrow-up"></i> Upgrade</small>
                                <?php elseif ($ehPior): ?>
                                    <small style="color: #ffc107;"><i class="fas fa-arrow-down"></i> Downgrade</small>
                                <?php else: ?>
                                    <small style="color: #6c757d;"><i class="fas fa-equals"></i> Mesmo valor</small>
                                <?php endif; ?>
                            </div>
                            <form action="<?php echo getActionUrl('trocar-plano'); ?>" method="POST" style="display: inline;" class="form-trocar-plano" data-plano-id="<?php echo $plano->getId(); ?>">
                                <input type="hidden" name="id_plano" value="<?php echo $plano->getId(); ?>">
                                <button type="button" class="btn-melhorar-plano" onclick="confirmarTrocaPlano(this);">
                                    <i class="fas fa-exchange-alt me-2"></i>Trocar Plano
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="perfil-card">
            <h3><i class="fas fa-cog me-2"></i>Configurações</h3>
            <button class="btn btn-editar-perfil" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
                <i class="fas fa-edit me-2"></i>Editar Perfil
            </button>
        </div>
    </div>

    <!-- Modal Editar Perfil -->
    <div class="modal fade" id="modalEditarPerfil" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo getActionUrl('usuario-atualizar-perfil'); ?>" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($usuario->getNome()); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario->getEmail()); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="telefone" class="form-control" value="<?php echo htmlspecialchars($usuario->getTelefone()); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nova Senha (deixe em branco para não alterar)</label>
                                <input type="password" name="senha" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Estado</label>
                                <input type="text" name="estado" class="form-control" value="<?php echo htmlspecialchars($usuario->getEstado() ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cidade</label>
                                <input type="text" name="cidade" class="form-control" value="<?php echo htmlspecialchars($usuario->getCidade() ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Bairro</label>
                                <input type="text" name="bairro" class="form-control" value="<?php echo htmlspecialchars($usuario->getBairro() ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rua</label>
                            <input type="text" name="rua" class="form-control" value="<?php echo htmlspecialchars($usuario->getRua() ?? ''); ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-editar-perfil">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmacao" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmacaoTitulo">Confirmar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalConfirmacaoTexto">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="modalConfirmacaoBtn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmarTrocaPlano(btn) {
            const form = btn.closest('form');
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            document.getElementById('modalConfirmacaoTitulo').textContent = 'Confirmar Troca de Plano';
            document.getElementById('modalConfirmacaoTexto').textContent = 'Deseja realmente trocar para este plano? O plano atual será cancelado e o novo será ativado imediatamente.';
            document.getElementById('modalConfirmacaoBtn').onclick = function() {
                form.submit();
            };
            modal.show();
        }

        function confirmarCancelarPlano() {
            const form = document.getElementById('formCancelarPlano');
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            document.getElementById('modalConfirmacaoTitulo').textContent = 'Confirmar Cancelamento';
            document.getElementById('modalConfirmacaoTexto').textContent = 'Deseja realmente cancelar seu plano? O plano permanecerá ativo até a data de término, mas não será renovado automaticamente.';
            document.getElementById('modalConfirmacaoBtn').className = 'btn btn-danger';
            document.getElementById('modalConfirmacaoBtn').onclick = function() {
                form.submit();
            };
            modal.show();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

