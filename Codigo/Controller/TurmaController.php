<?php

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/TurmaDAO.php";
include_once __DIR__ . "/../Model/helpers.php";

class TurmaController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new TurmaDAO();
    }

    public function cadastrar()
    {
        $idCurso = intval($_POST['id_curso'] ?? 0);
        $responsavel = intval($_POST['responsavel'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $dataInicio = $_POST['data_inicio'] ?? '';
        $dataFim = $_POST['data_fim'] ?? '';
        $horario = trim($_POST['horario'] ?? '');

        if (!$idCurso || !$responsavel || !$nome || !$dataInicio || !$dataFim) {
            $_SESSION['erro'] = 'Preencha todos os campos obrigatórios!';
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        }

        try {
            $capacidadeMaxima = intval($_POST['capacidade_maxima'] ?? 20);
            $turma = new Turma(null, $idCurso, $responsavel, $nome, $dataInicio, $dataFim, $horario, $capacidadeMaxima);
            $this->dao->cadastrar($turma);
            $_SESSION['sucesso'] = 'Turma cadastrada com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar turma: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao cadastrar turma. Tente novamente.';
            header('Location: ' . getViewUrl('cursos.php'));
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

    public function listarPorCurso($idCurso)
    {
        return $this->dao->readByCurso($idCurso);
    }

    public function atualizar()
    {
        Auth::requireAdmin();
        
        $id = intval($_POST['id'] ?? 0);
        $idCurso = intval($_POST['id_curso'] ?? 0);
        $responsavel = intval($_POST['responsavel'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $dataInicio = $_POST['data_inicio'] ?? '';
        $dataFim = $_POST['data_fim'] ?? '';
        $horario = trim($_POST['horario'] ?? '');
        $capacidadeMaxima = intval($_POST['capacidade_maxima'] ?? 20);

        if (!$id || !$idCurso || !$responsavel || !$nome || !$dataInicio || !$dataFim) {
            $_SESSION['erro'] = 'Preencha todos os campos obrigatórios!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $turma = new Turma($id, $idCurso, $responsavel, $nome, $dataInicio, $dataFim, $horario, $capacidadeMaxima);
            $this->dao->update($turma);
            $_SESSION['sucesso'] = 'Turma atualizada com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar turma: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao atualizar turma. Tente novamente.';
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
            $_SESSION['sucesso'] = 'Turma deletada com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao deletar turma: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao deletar turma. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }
}

