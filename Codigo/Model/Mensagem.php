<?php

class Mensagem
{
    private $id;
    private $idRemetente;
    private $idDestinatario;
    private $assunto;
    private $conteudo;
    private $dataEnvio;
    private $lida;
    private $tipo;
    private $idTurma;

    public function __construct($id, $idRemetente, $idDestinatario, $assunto, $conteudo, 
                                $dataEnvio = null, $lida = false, $tipo = 'geral', $idTurma = null)
    {
        $this->id = $id;
        $this->idRemetente = $idRemetente;
        $this->idDestinatario = $idDestinatario;
        $this->assunto = $assunto;
        $this->conteudo = $conteudo;
        $this->dataEnvio = $dataEnvio;
        $this->lida = $lida;
        $this->tipo = $tipo;
        $this->idTurma = $idTurma;
    }

    public function getId() { return $this->id; }
    public function getIdRemetente() { return $this->idRemetente; }
    public function getIdDestinatario() { return $this->idDestinatario; }
    public function getAssunto() { return $this->assunto; }
    public function getConteudo() { return $this->conteudo; }
    public function getDataEnvio() { return $this->dataEnvio; }
    public function getLida() { return $this->lida; }
    public function getTipo() { return $this->tipo; }
    public function getIdTurma() { return $this->idTurma; }
}

