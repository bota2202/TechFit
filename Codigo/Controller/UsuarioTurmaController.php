<?php

session_start();
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/UsuarioTurmaDAO.php";
include_once __DIR__ . "/../Model/helpers.php";

class UsuarioTurmaController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new UsuarioTurmaDAO();
    }

    public function matricular()
    {
        $idTurma = intval($_POST['id_turma'] ?? 0);
        $idUsuario = $_SESSION['usuario']['id'] ?? 0;

        if (!$idTurma || !$idUsuario) {
            $_SESSION['erro'] = 'Dados inválidos para matrícula!';
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        }

        try {
            $usuarioTurma = new UsuarioTurma($idTurma, $idUsuario);
            $this->dao->cadastrar($usuarioTurma);
            $_SESSION['sucesso'] = 'Matrícula realizada com sucesso!';
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao matricular: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao realizar matrícula. Tente novamente.';
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        }
    }

    public function listarPorUsuario($idUsuario)
    {
        return $this->dao->readByUsuario($idUsuario);
    }

    public function listarPorTurma($idTurma)
    {
        return $this->dao->readByTurma($idTurma);
    }
}

