<?php

class Curso
{
    public $id;
    public $nome;
    public $tipo;
    public $descricao;
    public $preco;

    public function __construct($id = null, $nome = null, $tipo = null, $descricao = null, $preco = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->descricao = $descricao;
        $this->preco = $preco;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }
    
    public function getTipo() { return $this->tipo; }
    public function setTipo($tipo) { $this->tipo = $tipo; }
    
    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
    
    public function getPreco() { return $this->preco; }
    public function setPreco($preco) { $this->preco = $preco; }
}

