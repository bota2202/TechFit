<?php

session_start();
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/PagamentoDAO.php";
include_once __DIR__ . "/../Model/helpers.php";

class PagamentoController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new PagamentoDAO();
    }

    public function registrar()
    {
        $idUsuario = intval($_POST['id_usuario'] ?? $_SESSION['usuario']['id'] ?? 0);
        $idPlano = intval($_POST['id_plano'] ?? 0);
        $tipo = intval($_POST['tipo'] ?? 1);
        $valor = floatval($_POST['valor'] ?? 0);

        if (!$idUsuario || !$idPlano || !$valor) {
            $_SESSION['erro'] = 'Dados inválidos para pagamento!';
            header('Location: ' . getViewUrl('planos.php'));
            exit;
        }

        try {
            $pagamento = new Pagamento(null, $idUsuario, $idPlano, $tipo, $valor);
            $this->dao->cadastrar($pagamento);
            $_SESSION['sucesso'] = 'Pagamento registrado com sucesso!';
            header('Location: ' . getViewUrl('planos.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao registrar pagamento: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao registrar pagamento. Tente novamente.';
            header('Location: ' . getViewUrl('planos.php'));
            exit;
        }
    }

    public function listarPorUsuario($idUsuario)
    {
        return $this->dao->readByUsuario($idUsuario);
    }
}

