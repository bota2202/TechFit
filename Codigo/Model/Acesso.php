<?php
/**
 * Model Acesso - TechFit
 * Para controle de acesso (QR Code, biometria, etc.)
 */

class Acesso
{
    private $id;
    private $idUsuario;
    private $idUnidade;
    private $dataAcesso;
    private $tipoAcesso; // 'entrada', 'saida'
    private $metodoAcesso; // 'qr_code', 'biometria', 'cartao'
    private $qrCode;

    public function __construct($id, $idUsuario, $idUnidade, $dataAcesso = null, 
                                $tipoAcesso = 'entrada', $metodoAcesso = 'qr_code', $qrCode = null)
    {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idUnidade = $idUnidade;
        $this->dataAcesso = $dataAcesso;
        $this->tipoAcesso = $tipoAcesso;
        $this->metodoAcesso = $metodoAcesso;
        $this->qrCode = $qrCode;
    }

    public function getId() { return $this->id; }
    public function getIdUsuario() { return $this->idUsuario; }
    public function getIdUnidade() { return $this->idUnidade; }
    public function getDataAcesso() { return $this->dataAcesso; }
    public function getTipoAcesso() { return $this->tipoAcesso; }
    public function getMetodoAcesso() { return $this->metodoAcesso; }
    public function getQrCode() { return $this->qrCode; }
}

