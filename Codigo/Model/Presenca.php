<?php

class Presenca
{
    public $id;
    public $idTurma;
    public $idUsuario;
    public $dataAula;
    public $presente;

    public function __construct(
        $id = null,
        $idTurma = null,
        $idUsuario = null,
        $dataAula = null,
        $presente = true
    ) {
        $this->id = $id;
        $this->idTurma = $idTurma;
        $this->idUsuario = $idUsuario;
        $this->dataAula = $dataAula;
        $this->presente = $presente;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getIdTurma() { return $this->idTurma; }
    public function setIdTurma($idTurma) { $this->idTurma = $idTurma; }
    
    public function getIdUsuario() { return $this->idUsuario; }
    public function setIdUsuario($idUsuario) { $this->idUsuario = $idUsuario; }
    
    public function getDataAula() { return $this->dataAula; }
    public function setDataAula($dataAula) { $this->dataAula = $dataAula; }
    
    public function getPresente() { return $this->presente; }
    public function setPresente($presente) { $this->presente = $presente; }
}

