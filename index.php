<?php

session_start();

define('BASE_PATH_FS', __DIR__);
define('CONTROLLER_PATH', BASE_PATH_FS . '/Codigo/Controller');
define('MODEL_PATH', BASE_PATH_FS . '/Codigo/Model');
define('VIEW_PATH', BASE_PATH_FS . '/Codigo/View');

if (!defined('BASE_PATH')) {
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $basePath = dirname($script);
    $basePath = rtrim($basePath, '/');
    if (empty($basePath) || $basePath === '.' || $basePath === '/') {
        $basePath = '';
    }
    define('BASE_PATH', $basePath);
}

require_once MODEL_PATH . '/config.php';
require_once MODEL_PATH . '/Conexao.php';
require_once MODEL_PATH . '/Usuario.php';
require_once MODEL_PATH . '/Unidade.php';
require_once MODEL_PATH . '/Plano.php';
require_once MODEL_PATH . '/Curso.php';
require_once MODEL_PATH . '/Turma.php';
require_once MODEL_PATH . '/UsuarioTurma.php';
require_once MODEL_PATH . '/UsuarioPlano.php';
require_once MODEL_PATH . '/Pagamento.php';
require_once MODEL_PATH . '/Presenca.php';
require_once MODEL_PATH . '/Log.php';
require_once MODEL_PATH . '/Treino.php';
require_once MODEL_PATH . '/Dieta.php';
require_once MODEL_PATH . '/ListaEspera.php';
require_once MODEL_PATH . '/AvaliacaoFisica.php';
require_once MODEL_PATH . '/Mensagem.php';
require_once MODEL_PATH . '/Acesso.php';

require_once MODEL_PATH . '/UsuarioDAO.php';
require_once MODEL_PATH . '/UnidadeDAO.php';
require_once MODEL_PATH . '/PlanoDAO.php';
require_once MODEL_PATH . '/CursoDAO.php';
require_once MODEL_PATH . '/TurmaDAO.php';
require_once MODEL_PATH . '/UsuarioTurmaDAO.php';
require_once MODEL_PATH . '/UsuarioPlanoDAO.php';
require_once MODEL_PATH . '/PagamentoDAO.php';
require_once MODEL_PATH . '/PresencaDAO.php';
require_once MODEL_PATH . '/LogDAO.php';
require_once MODEL_PATH . '/TreinoDAO.php';
require_once MODEL_PATH . '/DietaDAO.php';
require_once MODEL_PATH . '/ListaEsperaDAO.php';
require_once MODEL_PATH . '/AvaliacaoFisicaDAO.php';
require_once MODEL_PATH . '/MensagemDAO.php';
require_once MODEL_PATH . '/AcessoDAO.php';

require_once CONTROLLER_PATH . '/UsuarioController.php';
require_once CONTROLLER_PATH . '/UnidadeController.php';
require_once CONTROLLER_PATH . '/PlanoController.php';
require_once CONTROLLER_PATH . '/CursoController.php';
require_once CONTROLLER_PATH . '/TurmaController.php';
require_once CONTROLLER_PATH . '/UsuarioTurmaController.php';
require_once CONTROLLER_PATH . '/UsuarioPlanoController.php';
require_once CONTROLLER_PATH . '/PagamentoController.php';
require_once CONTROLLER_PATH . '/TreinoController.php';
require_once CONTROLLER_PATH . '/DietaController.php';
require_once CONTROLLER_PATH . '/AgendamentoController.php';
require_once CONTROLLER_PATH . '/AcessoController.php';
require_once CONTROLLER_PATH . '/MensagemController.php';
require_once CONTROLLER_PATH . '/AvaliacaoFisicaController.php';

$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'store':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UsuarioController();
            $controller->cadastrar();
        } else {
            header('Location: Codigo/View/cadastro.php');
            exit;
        }
        break;
        
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UsuarioController();
            $controller->login();
        } else {
            header('Location: Codigo/View/telalogin.php');
            exit;
        }
        break;
        
    case 'logout':
        require_once MODEL_PATH . '/helpers.php';
        session_destroy();
        session_start();
        $_SESSION['sucesso'] = 'Logout realizado com sucesso';
        header('Location: ' . getViewUrl('inicial.php'));
        exit;
    
    case 'usuario-buscar':
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            require_once MODEL_PATH . '/config.php';
            require_once MODEL_PATH . '/UsuarioDAO.php';
            
            if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
                http_response_code(403);
                echo json_encode(['erro' => 'Não autenticado']);
                exit;
            }
            
            if ($_SESSION['usuario']['tipo'] != TIPO_USUARIO_ADMIN) {
                http_response_code(403);
                echo json_encode(['erro' => 'Acesso negado. Apenas administradores podem acessar.']);
                exit;
            }
            
            $usuarioDAO = new UsuarioDAO();
            $id = intval($_GET['id'] ?? 0);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['erro' => 'ID inválido']);
                exit;
            }
            
            $sql = "SELECT * FROM usuarios WHERE id_usuario=?";
            $stmt = $usuarioDAO->conn->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                echo json_encode($row);
                exit;
            } else {
                http_response_code(404);
                echo json_encode(['erro' => 'Usuário não encontrado']);
                exit;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()]);
            exit;
        }
    
    case 'usuario-atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UsuarioController();
            $controller->atualizar();
        }
        break;
    
    case 'unidade-cadastrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UnidadeController();
            $controller->cadastrar();
        }
        break;
    
    case 'unidade-atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UnidadeController();
            $controller->atualizar();
        }
        break;
    
    case 'unidade-deletar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UnidadeController();
            $controller->deletar();
        }
        break;
    
    case 'curso-cadastrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new CursoController();
            $controller->cadastrar();
        }
        break;
    
    case 'curso-atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new CursoController();
            $controller->atualizar();
        }
        break;
    
    case 'curso-deletar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new CursoController();
            $controller->deletar();
        }
        break;
    
    case 'plano-cadastrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new PlanoController();
            $controller->cadastrar();
        }
        break;
    
    case 'plano-atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new PlanoController();
            $controller->atualizar();
        }
        break;
    
    case 'plano-deletar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new PlanoController();
            $controller->deletar();
        }
        break;
    
    case 'turma-cadastrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new TurmaController();
            $controller->cadastrar();
        }
        break;
    
    case 'turma-atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new TurmaController();
            $controller->atualizar();
        }
        break;
    
    case 'turma-deletar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new TurmaController();
            $controller->deletar();
        }
        break;
    
    case 'turma-horarios-disponiveis':
        header('Content-Type: application/json');
        require_once MODEL_PATH . '/Auth.php';
        require_once MODEL_PATH . '/TurmaDAO.php';
        Auth::requireAdmin();
        $turmaDAO = new TurmaDAO();
        $data = $_GET['data'] ?? '';
        if ($data) {
            $horarios = $turmaDAO->getHorariosOcupadosPorData($data);
            echo json_encode($horarios);
        } else {
            echo json_encode([]);
        }
        exit;
    
    case 'turma-listar-calendario':
        header('Content-Type: application/json');
        require_once MODEL_PATH . '/Auth.php';
        require_once MODEL_PATH . '/TurmaDAO.php';
        Auth::requireAdmin();
        $turmaDAO = new TurmaDAO();
        $dataInicio = $_GET['inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['fim'] ?? date('Y-m-t');
        $turmas = $turmaDAO->getTurmasPorPeriodo($dataInicio . ' 00:00:00', $dataFim . ' 23:59:59');
        echo json_encode($turmas);
        exit;
    
    case 'matricular':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UsuarioTurmaController();
            $controller->matricular();
        }
        break;
    
    case 'contratar-plano':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UsuarioPlanoController();
            $controller->contratar();
        }
        break;
    
    case 'melhorar-plano':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UsuarioPlanoController();
            $controller->melhorar();
        }
        break;
    
    case 'trocar-plano':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UsuarioPlanoController();
            $controller->trocar();
        }
        break;
    
    case 'cancelar-plano':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UsuarioPlanoController();
            $controller->cancelar();
        }
        break;
    
    case 'listar-planos':
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        require_once MODEL_PATH . '/PlanoDAO.php';
        try {
            $planoDAO = new PlanoDAO();
            $planos = $planoDAO->readAll();
            $planosArray = [];
            foreach ($planos as $plano) {
                $planosArray[] = [
                    'id' => $plano->getId(),
                    'preco' => floatval($plano->getPreco()),
                    'descricao' => $plano->getDescricao()
                ];
            }
            echo json_encode($planosArray, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Erro ao listar planos: " . $e->getMessage());
            echo json_encode(['erro' => 'Erro ao carregar planos'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    
    case 'listar-cursos':
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        require_once MODEL_PATH . '/CursoDAO.php';
        try {
            $cursoDAO = new CursoDAO();
            $cursos = $cursoDAO->readAll();
            $cursosArray = [];
            foreach ($cursos as $curso) {
                $cursosArray[] = [
                    'id' => $curso->getId(),
                    'nome' => $curso->getNome(),
                    'tipo' => $curso->getTipo(),
                    'descricao' => $curso->getDescricao(),
                    'preco' => floatval($curso->getPreco())
                ];
            }
            echo json_encode($cursosArray, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Erro ao listar cursos: " . $e->getMessage());
            echo json_encode(['erro' => 'Erro ao carregar cursos'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    
    case 'listar-turmas-curso':
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        require_once MODEL_PATH . '/Auth.php';
        require_once MODEL_PATH . '/TurmaDAO.php';
        require_once MODEL_PATH . '/UsuarioTurmaDAO.php';
        require_once MODEL_PATH . '/UsuarioPlanoDAO.php';
        try {
            Auth::requireAuth();
            $idCurso = intval($_GET['id_curso'] ?? 0);
            if (!$idCurso) {
                echo json_encode([]);
                exit;
            }
            $turmaDAO = new TurmaDAO();
            $usuarioTurmaDAO = new UsuarioTurmaDAO();
            $usuarioPlanoDAO = new UsuarioPlanoDAO();
            $idUsuario = $_SESSION['usuario']['id'] ?? 0;
            $temPlanoAtivo = $usuarioPlanoDAO->temPlanoAtivo($idUsuario);
            
            error_log("Buscando turmas para curso ID: $idCurso, Usuario ID: $idUsuario");
            $turmas = $turmaDAO->readByCurso($idCurso);
            error_log("Turmas encontradas: " . count($turmas));
            
            $turmasArray = [];
            foreach ($turmas as $turma) {
                if (!$turma || !$turma->getAtiva()) {
                    error_log("Turma ignorada - ID: " . ($turma ? $turma->getId() : 'null') . ", Ativa: " . ($turma ? ($turma->getAtiva() ? 'true' : 'false') : 'null'));
                    continue;
                }
                
                try {
                    $matriculados = $usuarioTurmaDAO->readByTurma($turma->getId());
                    $capacidade = $turma->getCapacidadeMaxima() ?: 20;
                    $ocupacao = count($matriculados);
                    $disponivel = $ocupacao < $capacidade;
                    $jaMatriculado = false;
                    foreach ($matriculados as $mat) {
                        if ($mat->getIdUsuario() == $idUsuario) {
                            $jaMatriculado = true;
                            break;
                        }
                    }
                    $dataInicio = $turma->getDataInicio();
                    $dataFim = $turma->getDataFim();
                    
                    if (!$dataInicio || $dataInicio == '0000-00-00' || $dataInicio == '0000-00-00 00:00:00' || strpos($dataInicio, '0000') === 0) {
                        $dataInicio = date('Y-m-d');
                    }
                    if (!$dataFim || $dataFim == '0000-00-00' || $dataFim == '0000-00-00 00:00:00' || strpos($dataFim, '0000') === 0) {
                        $dataFim = date('Y-m-d', strtotime('+1 month'));
                    }
                    
                    $turmasArray[] = [
                        'id' => $turma->getId(),
                        'nome' => $turma->getNome() ?? 'Turma sem nome',
                        'horario' => $turma->getHorario() ?? 'Horário a definir',
                        'data_inicio' => $dataInicio,
                        'data_fim' => $dataFim,
                        'capacidade' => $capacidade,
                        'ocupacao' => $ocupacao,
                        'disponivel' => $disponivel,
                        'ja_matriculado' => $jaMatriculado,
                        'tem_plano_ativo' => $temPlanoAtivo
                    ];
                } catch (Exception $e) {
                    error_log("Erro ao processar turma ID " . $turma->getId() . ": " . $e->getMessage());
                    continue;
                }
            }
            
            error_log("Turmas processadas para JSON: " . count($turmasArray));
            echo json_encode($turmasArray, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Erro ao listar turmas: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
            echo json_encode(['erro' => 'Erro ao carregar turmas: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;
    
    case 'verificar-plano-ativo':
        header('Content-Type: application/json');
        require_once MODEL_PATH . '/Auth.php';
        require_once MODEL_PATH . '/UsuarioPlanoDAO.php';
        Auth::requireAuth();
        $usuarioPlanoDAO = new UsuarioPlanoDAO();
        $idUsuario = $_SESSION['usuario']['id'] ?? 0;
        $temPlano = $usuarioPlanoDAO->temPlanoAtivo($idUsuario);
        $planoAtivo = null;
        if ($temPlano) {
            $planoAtivo = $usuarioPlanoDAO->getPlanoAtivo($idUsuario);
        }
        echo json_encode([
            'tem_plano' => $temPlano,
            'plano' => $planoAtivo
        ]);
        exit;
    
    case 'registrar-pagamento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new PagamentoController();
            $controller->registrar();
        }
        break;
    
    case 'treino-cadastrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new TreinoController();
            $controller->cadastrar();
        }
        break;
    
    case 'treino-atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new TreinoController();
            $controller->atualizar();
        }
        break;
    
    case 'treino-deletar':
        $controller = new TreinoController();
        $controller->deletar();
        break;
    
    case 'dieta-cadastrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new DietaController();
            $controller->cadastrar();
        }
        break;
    
    case 'dieta-atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new DietaController();
            $controller->atualizar();
        }
        break;
    
    case 'dieta-deletar':
        $controller = new DietaController();
        $controller->deletar();
        break;
    
    case 'agendar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new AgendamentoController();
            $controller->agendar();
        }
        break;
    
    case 'cancelar-agendamento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new AgendamentoController();
            $controller->cancelar();
        }
        break;
    
    case 'enviar-mensagem':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new MensagemController();
            $controller->enviar();
        }
        break;
    
    case 'marcar-mensagem-lida':
        $controller = new MensagemController();
        $controller->marcarLida();
        break;
    
    case 'responder-mensagem':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new MensagemController();
            $controller->responder();
        }
        break;
    
    case 'cadastrar-avaliacao':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new AvaliacaoFisicaController();
            $controller->cadastrar();
        }
        break;
        
    case 'home':
    default:
        header('Location: Codigo/View/inicial.php');
        exit;
}

