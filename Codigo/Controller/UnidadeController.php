<?php

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/UnidadeDAO.php";
include_once __DIR__ . "/../Model/helpers.php";

class UnidadeController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new UnidadeDAO();
    }

    public function cadastrar()
    {
        $estado = trim($_POST['estado'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $rua = trim($_POST['rua'] ?? '');
        $numero = intval($_POST['numero'] ?? 0);

        if (!$estado || !$cidade || !$bairro || !$rua || !$numero) {
            $_SESSION['erro'] = 'Preencha todos os campos!';
            header('Location: ' . getViewUrl('unidades.php'));
            exit;
        }

        try {
            $unidade = new Unidade(null, $estado, $cidade, $bairro, $rua, $numero);
            $this->dao->cadastrar($unidade);
            $_SESSION['sucesso'] = 'Unidade cadastrada com sucesso!';
            header('Location: ' . getViewUrl('unidades.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar unidade: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao cadastrar unidade. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }

    public function atualizar()
    {
        Auth::requireAdmin();
        
        $id = intval($_POST['id'] ?? 0);
        $estado = trim($_POST['estado'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $rua = trim($_POST['rua'] ?? '');
        $numero = intval($_POST['numero'] ?? 0);

        if (!$id || !$estado || !$cidade || !$bairro || !$rua || !$numero) {
            $_SESSION['erro'] = 'Preencha todos os campos!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $unidade = new Unidade($id, $estado, $cidade, $bairro, $rua, $numero);
            $this->dao->update($unidade);
            $_SESSION['sucesso'] = 'Unidade atualizada com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar unidade: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao atualizar unidade. Tente novamente.';
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
            $_SESSION['sucesso'] = 'Unidade deletada com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao deletar unidade: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao deletar unidade. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }

    public function listar()
    {
        return $this->dao->readAll();
    }

    public function buscar($id)
    {
        return $this->dao->readById($id);
    }
}

