<?php
/**
 * Model AvaliacaoFisica - TechFit
 */

class AvaliacaoFisica
{
    private $id;
    private $idUsuario;
    private $dataAvaliacao;
    private $peso;
    private $altura;
    private $imc;
    private $gorduraCorporal;
    private $massaMuscular;
    private $circunferenciaCintura;
    private $circunferenciaQuadril;
    private $observacoes;
    private $idInstrutor;

    public function __construct($id, $idUsuario, $dataAvaliacao, $peso, $altura, $imc = null, 
                                $gorduraCorporal = null, $massaMuscular = null, 
                                $circunferenciaCintura = null, $circunferenciaQuadril = null,
                                $observacoes = null, $idInstrutor = null)
    {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->dataAvaliacao = $dataAvaliacao;
        $this->peso = $peso;
        $this->altura = $altura;
        $this->imc = $imc ?: ($peso / ($altura * $altura));
        $this->gorduraCorporal = $gorduraCorporal;
        $this->massaMuscular = $massaMuscular;
        $this->circunferenciaCintura = $circunferenciaCintura;
        $this->circunferenciaQuadril = $circunferenciaQuadril;
        $this->observacoes = $observacoes;
        $this->idInstrutor = $idInstrutor;
    }

    public function getId() { return $this->id; }
    public function getIdUsuario() { return $this->idUsuario; }
    public function getDataAvaliacao() { return $this->dataAvaliacao; }
    public function getPeso() { return $this->peso; }
    public function getAltura() { return $this->altura; }
    public function getImc() { return $this->imc; }
    public function getGorduraCorporal() { return $this->gorduraCorporal; }
    public function getMassaMuscular() { return $this->massaMuscular; }
    public function getCircunferenciaCintura() { return $this->circunferenciaCintura; }
    public function getCircunferenciaQuadril() { return $this->circunferenciaQuadril; }
    public function getObservacoes() { return $this->observacoes; }
    public function getIdInstrutor() { return $this->idInstrutor; }
}

