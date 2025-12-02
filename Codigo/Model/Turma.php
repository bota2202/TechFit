<?php

class Turma
{
    public $id;
    public $idCurso;
    public $responsavel;
    public $nome;
    public $dataInicio;
    public $dataFim;
    public $horario;
    public $capacidadeMaxima;
    public $ativa;

    public function __construct(
        $id = null,
        $idCurso = null,
        $responsavel = null,
        $nome = null,
        $dataInicio = null,
        $dataFim = null,
        $horario = null,
        $capacidadeMaxima = 20,
        $ativa = true
    ) {
        $this->id = $id;
        $this->idCurso = $idCurso;
        $this->responsavel = $responsavel;
        $this->nome = $nome;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->horario = $horario;
        $this->capacidadeMaxima = $capacidadeMaxima;
        $this->ativa = $ativa;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getIdCurso() { return $this->idCurso; }
    public function setIdCurso($idCurso) { $this->idCurso = $idCurso; }
    
    public function getResponsavel() { return $this->responsavel; }
    public function setResponsavel($responsavel) { $this->responsavel = $responsavel; }
    
    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }
    
    public function getDataInicio() { return $this->dataInicio; }
    public function setDataInicio($dataInicio) { $this->dataInicio = $dataInicio; }
    
    public function getDataFim() { return $this->dataFim; }
    public function setDataFim($dataFim) { $this->dataFim = $dataFim; }
    
    public function getHorario() { return $this->horario; }
    public function setHorario($horario) { $this->horario = $horario; }
    
    public function getCapacidadeMaxima() { return $this->capacidadeMaxima; }
    public function setCapacidadeMaxima($capacidadeMaxima) { $this->capacidadeMaxima = $capacidadeMaxima; }
    
    public function getAtiva() { return $this->ativa; }
    public function setAtiva($ativa) { $this->ativa = $ativa; }
}

