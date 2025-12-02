<?php

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/CursoDAO.php";
include_once __DIR__ . "/../Model/helpers.php";

class CursoController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new CursoDAO();
    }

    public function listar()
    {
        return $this->dao->readAll();
    }

    public function buscar($id)
    {
        return $this->dao->readById($id);
    }

    public function listarPorTipo($tipo)
    {
        return $this->dao->readByTipo($tipo);
    }

    public function cadastrar()
    {
        Auth::requireAdmin();
        
        $nome = trim($_POST['nome'] ?? '');
        $tipo = trim($_POST['tipo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $preco = floatval($_POST['preco'] ?? 0);

        if (!$nome || !$tipo || !$descricao || !$preco) {
            $_SESSION['erro'] = 'Preencha todos os campos!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $curso = new Curso(null, $nome, $tipo, $descricao, $preco);
            $this->dao->cadastrar($curso);
            $_SESSION['sucesso'] = 'Curso cadastrado com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar curso: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao cadastrar curso. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }

    public function atualizar()
    {
        Auth::requireAdmin();
        
        $id = intval($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $tipo = trim($_POST['tipo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $preco = floatval($_POST['preco'] ?? 0);

        if (!$id || !$nome || !$tipo || !$descricao || !$preco) {
            $_SESSION['erro'] = 'Preencha todos os campos!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $curso = new Curso($id, $nome, $tipo, $descricao, $preco);
            $this->dao->update($curso);
            $_SESSION['sucesso'] = 'Curso atualizado com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar curso: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao atualizar curso. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }

    public function deletar()
    {
        Auth::requireAdmin();
        
        $id = intval($_POST['id'] ?? 0);

        if (!$id) {
            $_SESSION['erro'] = 'ID inválido!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $this->dao->delete($id);
            $_SESSION['sucesso'] = 'Curso deletado com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao deletar curso: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao deletar curso. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }
}

