<?php
/**
 * Controller Agendamento - TechFit
 */

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
require_once __DIR__ . "/../Model/Conexao.php";
require_once __DIR__ . "/../Model/UsuarioTurmaDAO.php";
require_once __DIR__ . "/../Model/ListaEsperaDAO.php";
require_once __DIR__ . "/../Model/ListaEspera.php";
require_once __DIR__ . "/../Model/TurmaDAO.php";
require_once __DIR__ . "/../Model/MensagemDAO.php";
require_once __DIR__ . "/../Model/Mensagem.php";
require_once __DIR__ . "/../Model/helpers.php";

class AgendamentoController
{
    private $usuarioTurmaDAO;
    private $listaEsperaDAO;
    private $turmaDAO;
    private $mensagemDAO;

    public function __construct()
    {
        $this->usuarioTurmaDAO = new UsuarioTurmaDAO();
        $this->listaEsperaDAO = new ListaEsperaDAO();
        $this->turmaDAO = new TurmaDAO();
        $this->mensagemDAO = new MensagemDAO();
    }

    public function agendar()
    {
        Auth::requireAuth();

        $idTurma = intval($_POST['id_turma'] ?? 0);
        $idUsuario = $_SESSION['usuario']['id'] ?? 0;

        if (!$idTurma || !$idUsuario) {
            $_SESSION['erro'] = 'Dados inválidos!';
            header('Location: ' . getViewUrl('agendamento.php'));
            exit;
        }

        try {
            $turma = $this->turmaDAO->readById($idTurma);
            if (!$turma) {
                $_SESSION['erro'] = 'Turma não encontrada!';
                header('Location: ' . getViewUrl('agendamento.php'));
                exit;
            }

            // Verifica capacidade
            $matriculados = $this->usuarioTurmaDAO->readByTurma($idTurma);
            $capacidadeMaxima = $turma->getCapacidadeMaxima() ?: 20;
            
            if (count($matriculados) >= $capacidadeMaxima) {
                // Adiciona à lista de espera
                $listaEspera = new ListaEspera(null, $idTurma, $idUsuario, null, 0, false);
                $this->listaEsperaDAO->cadastrar($listaEspera);
                $_SESSION['sucesso'] = 'Turma lotada! Você foi adicionado à lista de espera.';
            } else {
                // Matricula normalmente
                $usuarioTurma = new UsuarioTurma($idTurma, $idUsuario);
                $this->usuarioTurmaDAO->cadastrar($usuarioTurma);
                $_SESSION['sucesso'] = 'Agendamento realizado com sucesso!';
            }

            header('Location: ' . getViewUrl('agendamento.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao agendar: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao realizar agendamento. Tente novamente.';
            header('Location: ' . getViewUrl('agendamento.php'));
            exit;
        }
    }

    public function cancelar()
    {
        Auth::requireAuth();

        $idTurma = intval($_POST['id_turma'] ?? 0);
        $idUsuario = $_SESSION['usuario']['id'] ?? 0;

        if (!$idTurma || !$idUsuario) {
            $_SESSION['erro'] = 'Dados inválidos!';
            header('Location: ' . getViewUrl('agendamento.php'));
            exit;
        }

        try {
            $this->usuarioTurmaDAO->delete($idTurma, $idUsuario);
            
            // Verifica se há alguém na lista de espera e notifica
            $listaEspera = $this->listaEsperaDAO->readByTurma($idTurma);
            if (!empty($listaEspera)) {
                $proximo = $listaEspera[0];
                // Envia mensagem ao próximo da lista
                $mensagem = new Mensagem(
                    null,
                    null, // Sistema
                    $proximo->getIdUsuario(),
                    'Vaga disponível na turma',
                    'Uma vaga ficou disponível na turma. Acesse o sistema para confirmar sua matrícula.',
                    null,
                    false,
                    'geral',
                    $idTurma
                );
                $this->mensagemDAO->cadastrar($mensagem);
            }

            $_SESSION['sucesso'] = 'Agendamento cancelado com sucesso!';
            header('Location: ' . getViewUrl('agendamento.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao cancelar: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao cancelar agendamento. Tente novamente.';
            header('Location: ' . getViewUrl('agendamento.php'));
            exit;
        }
    }
}

