<?php
/**
 * Controller Acesso - TechFit
 */

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
require_once __DIR__ . "/../Model/Conexao.php";
require_once __DIR__ . "/../Model/AcessoDAO.php";
require_once __DIR__ . "/../Model/Acesso.php";
require_once __DIR__ . "/../Model/UsuarioDAO.php";

class AcessoController
{
    private $acessoDAO;
    private $usuarioDAO;

    public function __construct()
    {
        $this->acessoDAO = new AcessoDAO();
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function registrarAcesso()
    {
        // Pode ser chamado via API ou formulário
        $qrCode = $_POST['qr_code'] ?? $_GET['qr_code'] ?? '';
        $idUnidade = intval($_POST['id_unidade'] ?? $_GET['id_unidade'] ?? 0);
        $tipoAcesso = $_POST['tipo'] ?? $_GET['tipo'] ?? 'entrada';

        if (!$qrCode || !$idUnidade) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos']);
            exit;
        }

        // Decodifica QR Code (formato: usuario_id:timestamp:hash)
        $dados = explode(':', $qrCode);
        if (count($dados) < 2) {
            http_response_code(400);
            echo json_encode(['erro' => 'QR Code inválido']);
            exit;
        }

        $idUsuario = intval($dados[0]);

        try {
            $acesso = new Acesso(null, $idUsuario, $idUnidade, null, $tipoAcesso, 'qr_code', $qrCode);
            $idAcesso = $this->acessoDAO->registrarAcesso($acesso);
            
            echo json_encode([
                'sucesso' => true,
                'id_acesso' => $idAcesso,
                'mensagem' => 'Acesso registrado com sucesso'
            ]);
        } catch (Exception $e) {
            error_log("Erro ao registrar acesso: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao registrar acesso']);
        }
    }

    public function gerarQRCode()
    {
        Auth::requireAuth();

        $idUsuario = $_SESSION['usuario']['id'];
        $timestamp = time();
        $hash = md5($idUsuario . $timestamp . 'TechFit2024');
        
        $qrCode = $idUsuario . ':' . $timestamp . ':' . $hash;
        
        echo json_encode([
            'qr_code' => $qrCode,
            'usuario_id' => $idUsuario
        ]);
    }
}

