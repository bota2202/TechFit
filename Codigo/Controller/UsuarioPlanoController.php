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
        require_once __DIR__ . "/../Model/Auth.php";
        require_once __DIR__ . "/../Model/helpers.php";
        
        Auth::requireAuth();
        
        $idPlano = intval($_POST['id_plano'] ?? 0);
        $idUsuario = $_SESSION['usuario']['id'] ?? 0;
        $dataInicio = date('Y-m-d H:i:s');
        $dataFim = date('Y-m-d H:i:s', strtotime('+1 month'));

        if (!$idPlano || !$idUsuario) {
            $_SESSION['erro'] = 'Dados inválidos para contratação!';
            header('Location: ' . getViewUrl('planos.php'));
            exit;
        }

        if ($this->dao->temPlanoAtivo($idUsuario)) {
            $_SESSION['erro'] = 'Você já possui um plano ativo. Cancele o plano atual antes de contratar outro.';
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

    public function melhorar()
    {
        return $this->trocar();
    }

    public function trocar()
    {
        require_once __DIR__ . "/../Model/Auth.php";
        require_once __DIR__ . "/../Model/helpers.php";
        require_once __DIR__ . "/../Model/PlanoDAO.php";
        
        Auth::requireAuth();
        
        $idPlanoNovo = intval($_POST['id_plano'] ?? 0);
        $idUsuario = $_SESSION['usuario']['id'] ?? 0;

        if (!$idPlanoNovo || !$idUsuario) {
            $_SESSION['erro'] = 'Dados inválidos para trocar plano!';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }

        if (!$this->dao->temPlanoAtivo($idUsuario)) {
            $_SESSION['erro'] = 'Você não possui um plano ativo para trocar!';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }

        $planoAtivo = $this->dao->getPlanoAtivo($idUsuario);
        if ($planoAtivo['id_plano'] == $idPlanoNovo) {
            $_SESSION['erro'] = 'Você já possui este plano!';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }

        $planoDAO = new PlanoDAO();
        $planoNovo = $planoDAO->readById($idPlanoNovo);
        if (!$planoNovo) {
            $_SESSION['erro'] = 'Plano não encontrado!';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }

        try {
            $this->dao->delete($planoAtivo['id_plano'], $idUsuario);
            
            $dataInicio = date('Y-m-d H:i:s');
            $dataFim = date('Y-m-d H:i:s', strtotime('+1 month'));
            $usuarioPlano = new UsuarioPlano($idPlanoNovo, $idUsuario, $dataInicio, $dataFim);
            $this->dao->cadastrar($usuarioPlano);
            
            $precoAtual = $planoAtivo['preco_plano'] ?? 0;
            $precoNovo = $planoNovo->getPreco();
            $mensagem = 'Plano trocado com sucesso! Seu novo plano está ativo.';
            if ($precoNovo > $precoAtual) {
                $mensagem = 'Plano melhorado com sucesso! Seu novo plano está ativo.';
            } elseif ($precoNovo < $precoAtual) {
                $mensagem = 'Plano alterado com sucesso! Seu novo plano está ativo.';
            }
            
            $_SESSION['sucesso'] = $mensagem;
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao trocar plano: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao trocar plano. Tente novamente.';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }
    }

    public function cancelar()
    {
        require_once __DIR__ . "/../Model/Auth.php";
        require_once __DIR__ . "/../Model/helpers.php";
        
        Auth::requireAuth();
        
        $idUsuario = $_SESSION['usuario']['id'] ?? 0;

        if (!$idUsuario) {
            $_SESSION['erro'] = 'Dados inválidos para cancelar plano!';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }

        if (!$this->dao->temPlanoAtivo($idUsuario)) {
            $_SESSION['erro'] = 'Você não possui um plano ativo para cancelar!';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }

        try {
            $planoAtivo = $this->dao->getPlanoAtivo($idUsuario);
            $this->dao->cancelarPlanoAtivo($idUsuario);
            
            $_SESSION['sucesso'] = 'Plano cancelado com sucesso! O plano permanecerá ativo até a data de término.';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao cancelar plano: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao cancelar plano. Tente novamente.';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }
    }
}

