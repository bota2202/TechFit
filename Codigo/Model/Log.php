<?php

class Log
{
    public $id;
    public $idUsuario;
    public $acao;
    public $dataAcao;

    public function __construct($id = null, $idUsuario = null, $acao = null, $dataAcao = null)
    {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->acao = $acao;
        $this->dataAcao = $dataAcao;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getIdUsuario() { return $this->idUsuario; }
    public function setIdUsuario($idUsuario) { $this->idUsuario = $idUsuario; }
    
    public function getAcao() { return $this->acao; }
    public function setAcao($acao) { $this->acao = $acao; }
    
    public function getDataAcao() { return $this->dataAcao; }
    public function setDataAcao($dataAcao) { $this->dataAcao = $dataAcao; }
}

