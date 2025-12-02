<?php

class UsuarioPlano
{
    public $idPlano;
    public $idUsuario;
    public $dataInicio;
    public $dataFim;

    public function __construct($idPlano = null, $idUsuario = null, $dataInicio = null, $dataFim = null)
    {
        $this->idPlano = $idPlano;
        $this->idUsuario = $idUsuario;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
    }

    public function getIdPlano() { return $this->idPlano; }
    public function setIdPlano($idPlano) { $this->idPlano = $idPlano; }
    
    public function getIdUsuario() { return $this->idUsuario; }
    public function setIdUsuario($idUsuario) { $this->idUsuario = $idUsuario; }
    
    public function getDataInicio() { return $this->dataInicio; }
    public function setDataInicio($dataInicio) { $this->dataInicio = $dataInicio; }
    
    public function getDataFim() { return $this->dataFim; }
    public function setDataFim($dataFim) { $this->dataFim = $dataFim; }
}

