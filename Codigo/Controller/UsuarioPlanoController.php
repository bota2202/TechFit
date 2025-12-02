<?php

session_start();
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/UsuarioPlanoDAO.php";
include_once __DIR__ . "/../Model/helpers.php";

class UsuarioPlanoController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new UsuarioPlanoDAO();
    }

    public function contratar()
    {
        $idPlano = intval($_POST['id_plano'] ?? 0);
        $idUsuario = $_SESSION['usuario']['id'] ?? 0;
        $dataInicio = date('Y-m-d H:i:s');
        $dataFim = date('Y-m-d H:i:s', strtotime('+1 month'));

        if (!$idPlano || !$idUsuario) {
            $_SESSION['erro'] = 'Dados inválidos para contratação!';
            header('Location: ' . getViewUrl('planos.php'));
            exit;
        }

        try {
            $usuarioPlano = new UsuarioPlano($idPlano, $idUsuario, $dataInicio, $dataFim);
            $this->dao->cadastrar($usuarioPlano);
            $_SESSION['sucesso'] = 'Plano contratado com sucesso!';
            header('Location: ' . getViewUrl('planos.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao contratar plano: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao contratar plano. Tente novamente.';
            header('Location: ' . getViewUrl('planos.php'));
            exit;
        }
    }

    public function listarPorUsuario($idUsuario)
    {
        return $this->dao->readByUsuario($idUsuario);
    }
}

