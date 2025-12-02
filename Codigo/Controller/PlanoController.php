<?php

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/PlanoDAO.php";
include_once __DIR__ . "/../Model/helpers.php";

class PlanoController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new PlanoDAO();
    }

    public function listar()
    {
        return $this->dao->readAll();
    }

    public function buscar($id)
    {
        return $this->dao->readById($id);
    }

    public function cadastrar()
    {
        Auth::requireAdmin();
        
        $preco = floatval($_POST['preco'] ?? 0);
        $descricao = trim($_POST['descricao'] ?? '');

        if (!$preco || !$descricao) {
            $_SESSION['erro'] = 'Preencha todos os campos!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $plano = new Plano(null, $preco, $descricao);
            $this->dao->cadastrar($plano);
            $_SESSION['sucesso'] = 'Plano cadastrado com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar plano: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao cadastrar plano. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }

    public function atualizar()
    {
        Auth::requireAdmin();
        
        $id = intval($_POST['id'] ?? 0);
        $preco = floatval($_POST['preco'] ?? 0);
        $descricao = trim($_POST['descricao'] ?? '');

        if (!$id || !$preco || !$descricao) {
            $_SESSION['erro'] = 'Preencha todos os campos!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $plano = new Plano($id, $preco, $descricao);
            $this->dao->update($plano);
            $_SESSION['sucesso'] = 'Plano atualizado com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar plano: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao atualizar plano. Tente novamente.';
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
            $_SESSION['sucesso'] = 'Plano deletado com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao deletar plano: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao deletar plano. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }
}

