<?php
/**
 * Controller Mensagem - TechFit
 */

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
require_once __DIR__ . "/../Model/Conexao.php";
require_once __DIR__ . "/../Model/MensagemDAO.php";
require_once __DIR__ . "/../Model/Mensagem.php";
require_once __DIR__ . "/../Model/UsuarioDAO.php";
require_once __DIR__ . "/../Model/UsuarioTurmaDAO.php";
require_once __DIR__ . "/../Model/helpers.php";

class MensagemController
{
    private $mensagemDAO;
    private $usuarioDAO;
    private $usuarioTurmaDAO;

    public function __construct()
    {
        $this->mensagemDAO = new MensagemDAO();
        $this->usuarioDAO = new UsuarioDAO();
        $this->usuarioTurmaDAO = new UsuarioTurmaDAO();
    }

    public function enviar()
    {
        Auth::requireAuth();

        $idDestinatario = intval($_POST['id_destinatario'] ?? 0);
        $assunto = trim($_POST['assunto'] ?? '');
        $conteudo = trim($_POST['conteudo'] ?? '');
        $tipo = $_POST['tipo'] ?? 'geral';
        $idTurma = intval($_POST['id_turma'] ?? 0);

        if (!$assunto || !$conteudo) {
            $_SESSION['erro'] = 'Preencha todos os campos!';
            header('Location: ' . getViewUrl('mensagens.php'));
            exit;
        }

        try {
            $idRemetente = $_SESSION['usuario']['id'];

            if ($tipo === 'turma' && $idTurma) {
                // Envia para todos da turma
                $matriculados = $this->usuarioTurmaDAO->readByTurma($idTurma);
                foreach ($matriculados as $matricula) {
                    $mensagem = new Mensagem(
                        null,
                        $idRemetente,
                        $matricula->getIdUsuario(),
                        $assunto,
                        $conteudo,
                        null,
                        false,
                        'turma',
                        $idTurma
                    );
                    $this->mensagemDAO->cadastrar($mensagem);
                }
                $_SESSION['sucesso'] = 'Mensagem enviada para todos os alunos da turma!';
            } elseif ($idDestinatario) {
                // Mensagem individual - redireciona para o chat
                $mensagem = new Mensagem(
                    null,
                    $idRemetente,
                    $idDestinatario,
                    $assunto,
                    $conteudo,
                    null,
                    false,
                    $tipo
                );
                $this->mensagemDAO->cadastrar($mensagem);
                $_SESSION['sucesso'] = 'Mensagem enviada com sucesso!';
                // Redireciona para o chat com o destinatário
                header('Location: ' . getViewUrl('chat.php') . '?usuario=' . $idDestinatario);
                exit;
            } else {
                $_SESSION['erro'] = 'Selecione um destinatário ou turma!';
            }

            header('Location: ' . getViewUrl('mensagens.php'));
            exit;
        } catch (Exception $e) {
            error_log("Erro ao enviar mensagem: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao enviar mensagem. Tente novamente.';
            header('Location: ' . getViewUrl('mensagens.php'));
            exit;
        }
    }

    public function marcarLida()
    {
        Auth::requireAuth();

        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            exit;
        }

        try {
            $this->mensagemDAO->marcarComoLida($id);
            echo json_encode(['sucesso' => true]);
        } catch (Exception $e) {
            error_log("Erro ao marcar mensagem como lida: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao processar']);
        }
    }

    public function responder()
    {
        Auth::requireAuth();

        $idDestinatario = intval($_POST['id_destinatario'] ?? 0);
        $conteudo = trim($_POST['conteudo'] ?? '');
        $assunto = trim($_POST['assunto'] ?? 'Nova mensagem');

        if (!$idDestinatario || !$conteudo) {
            $_SESSION['erro'] = 'Preencha a mensagem!';
            header('Location: ' . getViewUrl('chat.php') . '?usuario=' . $idDestinatario);
            exit;
        }

        try {
            $idRemetente = $_SESSION['usuario']['id'];
            $mensagem = new Mensagem(
                null,
                $idRemetente,
                $idDestinatario,
                $assunto,
                $conteudo,
                null,
                false,
                'geral'
            );
            $this->mensagemDAO->cadastrar($mensagem);
            
            // Marca mensagens anteriores como lidas
            $conversa = $this->mensagemDAO->readConversa($idRemetente, $idDestinatario);
            foreach ($conversa as $msg) {
                if ($msg->getIdDestinatario() == $idRemetente && !$msg->getLida()) {
                    $this->mensagemDAO->marcarComoLida($msg->getId());
                }
            }

            header('Location: ' . getViewUrl('chat.php') . '?usuario=' . $idDestinatario);
            exit;
        } catch (Exception $e) {
            error_log("Erro ao responder mensagem: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao enviar mensagem. Tente novamente.';
            header('Location: ' . getViewUrl('chat.php') . '?usuario=' . $idDestinatario);
            exit;
        }
    }
}

