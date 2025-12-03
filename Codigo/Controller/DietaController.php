<?php

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
require_once __DIR__ . "/../Model/Conexao.php";
require_once __DIR__ . "/../Model/Dieta.php";
require_once __DIR__ . "/../Model/DietaDAO.php";
require_once __DIR__ . "/../Model/UsuarioDAO.php";
require_once __DIR__ . "/../Model/helpers.php";

class DietaController
{
    private $dietaDAO;
    private $usuarioDAO;

    public function __construct()
    {
        $this->dietaDAO = new DietaDAO();
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
            $dieta = new Dieta(null, $idUsuario, $titulo, $descricao, $observacoes ?: null);
            $this->dietaDAO->cadastrar($dieta);
            $_SESSION['sucesso'] = 'Dieta cadastrada com sucesso!';
        } catch (Exception $e) {
            error_log("Erro ao cadastrar dieta: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao cadastrar dieta. Tente novamente.';
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
            $dieta = $this->dietaDAO->readById($id);
            if (!$dieta) {
                $_SESSION['erro'] = 'Dieta não encontrada!';
                header('Location: ' . getViewUrl('dashboard_admin.php'));
                exit;
            }

            $dietaAtualizada = new Dieta(
                $id,
                $dieta->getIdUsuario(),
                $titulo,
                $descricao,
                $observacoes ?: null
            );
            
            $this->dietaDAO->update($dietaAtualizada);
            $_SESSION['sucesso'] = 'Dieta atualizada com sucesso!';
        } catch (Exception $e) {
            error_log("Erro ao atualizar dieta: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao atualizar dieta. Tente novamente.';
        }

        header('Location: ' . getViewUrl('dashboard_admin.php'));
        exit;
    }

    public function deletar()
    {
        Auth::requireAdmin();

        $id = intval($_GET['id'] ?? 0);

        if (!$id) {
            $_SESSION['erro'] = 'ID da dieta não informado!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $this->dietaDAO->delete($id);
            $_SESSION['sucesso'] = 'Dieta deletada com sucesso!';
        } catch (Exception $e) {
            error_log("Erro ao deletar dieta: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao deletar dieta. Tente novamente.';
        }

        header('Location: ' . getViewUrl('dashboard_admin.php'));
        exit;
    }
}

