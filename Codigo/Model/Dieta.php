<?php
/**
 * Model Dieta - TechFit
 */

class Dieta
{
    private $id;
    private $idUsuario;
    private $titulo;
    private $descricao;
    private $observacoes;
    private $dataCriacao;

    public function __construct($id, $idUsuario, $titulo, $descricao, $observacoes = null, $dataCriacao = null)
    {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->observacoes = $observacoes;
        $this->dataCriacao = $dataCriacao;
    }

    public function getId() { return $this->id; }
    public function getIdUsuario() { return $this->idUsuario; }
    public function getTitulo() { return $this->titulo; }
    public function getDescricao() { return $this->descricao; }
    public function getObservacoes() { return $this->observacoes; }
    public function getDataCriacao() { return $this->dataCriacao; }
}

