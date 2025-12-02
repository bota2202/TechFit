<?php
/**
 * Controller AvaliacaoFisica - TechFit
 */

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
require_once __DIR__ . "/../Model/Conexao.php";
require_once __DIR__ . "/../Model/AvaliacaoFisicaDAO.php";
require_once __DIR__ . "/../Model/AvaliacaoFisica.php";
require_once __DIR__ . "/../Model/helpers.php";

class AvaliacaoFisicaController
{
    private $avaliacaoDAO;

    public function __construct()
    {
        $this->avaliacaoDAO = new AvaliacaoFisicaDAO();
    }

    public function cadastrar()
    {
        Auth::requireAdmin();

        $idUsuario = intval($_POST['id_usuario'] ?? 0);
        $peso = floatval($_POST['peso'] ?? 0);
        $altura = floatval($_POST['altura'] ?? 0);
        $gorduraCorporal = !empty($_POST['gordura_corporal']) ? floatval($_POST['gordura_corporal']) : null;
        $massaMuscular = !empty($_POST['massa_muscular']) ? floatval($_POST['massa_muscular']) : null;
        $circunferenciaCintura = !empty($_POST['circunferencia_cintura']) ? floatval($_POST['circunferencia_cintura']) : null;
        $circunferenciaQuadril = !empty($_POST['circunferencia_quadril']) ? floatval($_POST['circunferencia_quadril']) : null;
        $observacoes = trim($_POST['observacoes'] ?? '');
        $dataAvaliacao = $_POST['data_avaliacao'] ?? date('Y-m-d H:i:s');
        $idInstrutor = $_SESSION['usuario']['id'];

        if (!$idUsuario || !$peso || !$altura) {
            $_SESSION['erro'] = 'Preencha peso e altura!';
            header('Location: ' . getViewUrl('avaliacoes.php'));
            exit;
        }

        try {
            $avaliacao = new AvaliacaoFisica(
                null,
                $idUsuario,
                $dataAvaliacao,
                $peso,
                $altura,
                null, // IMC calculado automaticamente
                $gorduraCorporal,
                $massaMuscular,
                $circunferenciaCintura,
                $circunferenciaQuadril,
                $observacoes ?: null,
                $idInstrutor
            );

            $this->avaliacaoDAO->cadastrar($avaliacao);
            $_SESSION['sucesso'] = 'Avaliação física registrada com sucesso!';
            header('Location: ' . getViewUrl('avaliacoes.php'));
            exit;
        } catch (Exception $e) {
            error_log("Erro ao cadastrar avaliação: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao registrar avaliação. Tente novamente.';
            header('Location: ' . getViewUrl('avaliacoes.php'));
            exit;
        }
    }
}

