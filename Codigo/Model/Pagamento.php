<?php

class Pagamento
{
    public $id;
    public $idUsuario;
    public $idPlano;
    public $tipo;
    public $valor;
    public $dataPagamento;

    public function __construct(
        $id = null,
        $idUsuario = null,
        $idPlano = null,
        $tipo = null,
        $valor = null,
        $dataPagamento = null
    ) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idPlano = $idPlano;
        $this->tipo = $tipo;
        $this->valor = $valor;
        $this->dataPagamento = $dataPagamento;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getIdUsuario() { return $this->idUsuario; }
    public function setIdUsuario($idUsuario) { $this->idUsuario = $idUsuario; }
    
    public function getIdPlano() { return $this->idPlano; }
    public function setIdPlano($idPlano) { $this->idPlano = $idPlano; }
    
    public function getTipo() { return $this->tipo; }
    public function setTipo($tipo) { $this->tipo = $tipo; }
    
    public function getValor() { return $this->valor; }
    public function setValor($valor) { $this->valor = $valor; }
    
    public function getDataPagamento() { return $this->dataPagamento; }
    public function setDataPagamento($dataPagamento) { $this->dataPagamento = $dataPagamento; }
}

