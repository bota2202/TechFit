<?php

class Unidade
{
    public $id;
    public $estado;
    public $cidade;
    public $bairro;
    public $rua;
    public $numero;

    public function __construct(
        $id = null,
        $estado = null,
        $cidade = null,
        $bairro = null,
        $rua = null,
        $numero = null
    ) {
        $this->id = $id;
        $this->estado = $estado;
        $this->cidade = $cidade;
        $this->bairro = $bairro;
        $this->rua = $rua;
        $this->numero = $numero;
    }

    // Getters e Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
    
    public function getCidade() { return $this->cidade; }
    public function setCidade($cidade) { $this->cidade = $cidade; }
    
    public function getBairro() { return $this->bairro; }
    public function setBairro($bairro) { $this->bairro = $bairro; }
    
    public function getRua() { return $this->rua; }
    public function setRua($rua) { $this->rua = $rua; }
    
    public function getNumero() { return $this->numero; }
    public function setNumero($numero) { $this->numero = $numero; }
}

