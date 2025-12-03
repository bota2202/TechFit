<?php

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
require_once __DIR__ . "/../Model/Conexao.php";
require_once __DIR__ . "/../Model/Treino.php";
require_once __DIR__ . "/../Model/TreinoDAO.php";
require_once __DIR__ . "/../Model/UsuarioDAO.php";
require_once __DIR__ . "/../Model/helpers.php";

class TreinoController
{
    private $treinoDAO;
    private $usuarioDAO;

    public function __construct()
    {
        $this->treinoDAO = new TreinoDAO();
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function cadastrar()
    {
        Auth::requireAdmin();

        $idUsuario = intval($_POST['id_usuario'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');

        if (!$idUsuario || !$titulo || !$descricao) {
            $_SESSION['erro'] = 'Preencha todos os campos obrigatórios!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        $usuario = $this->usuarioDAO->readById($idUsuario);
        if (!$usuario) {
            $_SESSION['erro'] = 'Usuário não encontrado!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $treino = new Treino(null, $idUsuario, $titulo, $descricao, $observacoes ?: null);
            $this->treinoDAO->cadastrar($treino);
            $_SESSION['sucesso'] = 'Treino cadastrado com sucesso!';
        } catch (Exception $e) {
            error_log("Erro ao cadastrar treino: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao cadastrar treino. Tente novamente.';
        }

        header('Location: ' . getViewUrl('dashboard_admin.php'));
        exit;
    }

    public function atualizar()
    {
        Auth::requireAdmin();

        $id = intval($_POST['id'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');

        if (!$id || !$titulo || !$descricao) {
            $_SESSION['erro'] = 'Preencha todos os campos obrigatórios!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $treino = $this->treinoDAO->readById($id);
            if (!$treino) {
                $_SESSION['erro'] = 'Treino não encontrado!';
                header('Location: ' . getViewUrl('dashboard_admin.php'));
                exit;
            }

            $treinoAtualizado = new Treino(
                $id,
                $treino->getIdUsuario(),
                $titulo,
                $descricao,
                $observacoes ?: null
            );
            
            $this->treinoDAO->update($treinoAtualizado);
            $_SESSION['sucesso'] = 'Treino atualizado com sucesso!';
        } catch (Exception $e) {
            error_log("Erro ao atualizar treino: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao atualizar treino. Tente novamente.';
        }

        header('Location: ' . getViewUrl('dashboard_admin.php'));
        exit;
    }

    public function deletar()
    {
        Auth::requireAdmin();

        $id = intval($_GET['id'] ?? 0);

        if (!$id) {
            $_SESSION['erro'] = 'ID do treino não informado!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $this->treinoDAO->delete($id);
            $_SESSION['sucesso'] = 'Treino deletado com sucesso!';
        } catch (Exception $e) {
            error_log("Erro ao deletar treino: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao deletar treino. Tente novamente.';
        }

        header('Location: ' . getViewUrl('dashboard_admin.php'));
        exit;
    }
}

