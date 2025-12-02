<?php

class Plano
{
    public $id;
    public $preco;
    public $descricao;

    public function __construct($id = null, $preco = null, $descricao = null)
    {
        $this->id = $id;
        $this->preco = $preco;
        $this->descricao = $descricao;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getPreco() { return $this->preco; }
    public function setPreco($preco) { $this->preco = $preco; }
    
    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
}

