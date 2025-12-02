<?php

class UsuarioTurma
{
    public $idTurma;
    public $idUsuario;
    public $dataMatricula;

    public function __construct($idTurma = null, $idUsuario = null, $dataMatricula = null)
    {
        $this->idTurma = $idTurma;
        $this->idUsuario = $idUsuario;
        $this->dataMatricula = $dataMatricula;
    }

    public function getIdTurma() { return $this->idTurma; }
    public function setIdTurma($idTurma) { $this->idTurma = $idTurma; }
    
    public function getIdUsuario() { return $this->idUsuario; }
    public function setIdUsuario($idUsuario) { $this->idUsuario = $idUsuario; }
    
    public function getDataMatricula() { return $this->dataMatricula; }
    public function setDataMatricula($dataMatricula) { $this->dataMatricula = $dataMatricula; }
}

