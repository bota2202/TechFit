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
        require_once __DIR__ . "/../Model/Auth.php";
        require_once __DIR__ . "/../Model/helpers.php";
        require_once __DIR__ . "/../Model/UsuarioPlanoDAO.php";
        require_once __DIR__ . "/../Model/TurmaDAO.php";
        require_once __DIR__ . "/../Model/UsuarioTurma.php";
        
        Auth::requireAuth();
        
        $idTurma = intval($_POST['id_turma'] ?? 0);
        $idUsuario = $_SESSION['usuario']['id'] ?? 0;

        if (!$idTurma || !$idUsuario) {
            $_SESSION['erro'] = 'Dados inválidos para matrícula!';
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        }

        $usuarioPlanoDAO = new UsuarioPlanoDAO();
        if (!$usuarioPlanoDAO->temPlanoAtivo($idUsuario)) {
            $_SESSION['erro'] = 'Você precisa ter um plano ativo para se matricular em cursos!';
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        }

        $turmaDAO = new TurmaDAO();
        $turma = $turmaDAO->readById($idTurma);
        if (!$turma) {
            $_SESSION['erro'] = 'Turma não encontrada!';
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        }
        
        // Verifica se a turma está ativa (considera null como ativa por padrão)
        if ($turma->getAtiva() === false) {
            $_SESSION['erro'] = 'Turma inativa!';
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        }

        $matriculados = $this->dao->readByTurma($idTurma);
        $capacidade = $turma->getCapacidadeMaxima() ?: 20;
        if (count($matriculados) >= $capacidade) {
            $_SESSION['erro'] = 'Turma lotada! Não há vagas disponíveis.';
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        }

        foreach ($matriculados as $mat) {
            if ($mat->getIdUsuario() == $idUsuario) {
                $_SESSION['erro'] = 'Você já está matriculado nesta turma!';
                header('Location: ' . getViewUrl('cursos.php'));
                exit;
            }
        }

        try {
            $usuarioTurma = new UsuarioTurma($idTurma, $idUsuario);
            $this->dao->cadastrar($usuarioTurma);
            $_SESSION['sucesso'] = 'Matrícula realizada com sucesso!';
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao matricular usuário $idUsuario na turma $idTurma: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['erro'] = 'Erro ao realizar matrícula: ' . $e->getMessage();
            header('Location: ' . getViewUrl('cursos.php'));
            exit;
        } catch (Exception $e) {
            error_log("Erro geral ao matricular: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
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

