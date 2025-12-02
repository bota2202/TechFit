<?php
/**
 * Model ListaEspera - TechFit
 */

class ListaEspera
{
    private $id;
    private $idTurma;
    private $idUsuario;
    private $dataInscricao;
    private $prioridade;
    private $notificado;

    public function __construct($id, $idTurma, $idUsuario, $dataInscricao = null, $prioridade = 0, $notificado = false)
    {
        $this->id = $id;
        $this->idTurma = $idTurma;
        $this->idUsuario = $idUsuario;
        $this->dataInscricao = $dataInscricao;
        $this->prioridade = $prioridade;
        $this->notificado = $notificado;
    }

    public function getId() { return $this->id; }
    public function getIdTurma() { return $this->idTurma; }
    public function getIdUsuario() { return $this->idUsuario; }
    public function getDataInscricao() { return $this->dataInscricao; }
    public function getPrioridade() { return $this->prioridade; }
    public function getNotificado() { return $this->notificado; }
}

