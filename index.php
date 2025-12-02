<?php
/**
 * Router Principal - TechFit
 * Processa todas as requisições e direciona para os controllers apropriados
 */

session_start();

// Define constantes de caminho (File System)
define('BASE_PATH_FS', __DIR__);
define('CONTROLLER_PATH', BASE_PATH_FS . '/Codigo/Controller');
define('MODEL_PATH', BASE_PATH_FS . '/Codigo/Model');
define('VIEW_PATH', BASE_PATH_FS . '/Codigo/View');

// Calcula BASE_PATH para URLs (caminho relativo na web)
if (!defined('BASE_PATH')) {
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $basePath = dirname($script);
    $basePath = rtrim($basePath, '/');
    if (empty($basePath) || $basePath === '.' || $basePath === '/') {
        $basePath = '';
    }
    define('BASE_PATH', $basePath);
}

// Inclui autoload básico
require_once MODEL_PATH . '/config.php';
require_once MODEL_PATH . '/Conexao.php';

// Models
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

// DAOs
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

// Controllers
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

// Obtém a ação da requisição
$action = $_GET['action'] ?? 'home';

// Roteamento
switch ($action) {
    // Usuário
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
    
    // Unidade
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
    
    // Curso
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
    
    // Plano
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
    
    // Turma
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
    
    // Usuario Turma
    case 'matricular':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UsuarioTurmaController();
            $controller->matricular();
        }
        break;
    
    // Usuario Plano
    case 'contratar-plano':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new UsuarioPlanoController();
            $controller->contratar();
        }
        break;
    
    // Pagamento
    case 'registrar-pagamento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new PagamentoController();
            $controller->registrar();
        }
        break;
    
    // Treino
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
    
    // Dieta
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
    
    // Agendamento
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
    
    // Acesso
    // Mensagem
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
    
    // Avaliação Física
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

