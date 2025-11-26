<?php

include_once __DIR__ . "/../Model/Conexao.php";

class Usuario
{
    private $id_usuario;
    private $email_usuario;
    private $senha_usuario_hash;
    private $nome_usuario;
    private $telefone_usuario;
    private $cpf_usuario;
    private $tipo_usuario;
    private $estado_usuario;
    private $cidade_usuario;
    private $endereco_usuario;

    public function __construct(
        $email = null,
        $senha_hash = null,
        $nome = null,
        $telefone = null,
        $cpf = null,
        $tipo = null,
        $estado = null,
        $cidade = null,
        $endereco = null,
        $id = null
    ) {
        $this->id_usuario = $id;
        $this->email_usuario = $email;
        $this->senha_usuario_hash = $senha_hash;
        $this->nome_usuario = $nome;
        $this->telefone_usuario = $telefone;
        $this->cpf_usuario = $cpf;
        $this->tipo_usuario = $tipo;
        $this->estado_usuario = $estado;
        $this->cidade_usuario = $cidade;
        $this->endereco_usuario = $endereco;
    }

    public function getIdUsuario()
    {
        return $this->id_usuario;
    }
    public function getEmailUsuario()
    {
        return $this->email_usuario;
    }
    public function getSenhaUsuarioHash()
    {
        return $this->senha_usuario_hash;
    }
    public function getNomeUsuario()
    {
        return $this->nome_usuario;
    }
    public function getTelefoneUsuario()
    {
        return $this->telefone_usuario;
    }
    public function getCpfUsuario()
    {
        return $this->cpf_usuario;
    }
    public function getTipoUsuario()
    {
        return $this->tipo_usuario;
    }
    public function getEstadoUsuario()
    {
        return $this->estado_usuario;
    }
    public function getCidadeUsuario()
    {
        return $this->cidade_usuario;
    }
    public function getEnderecoUsuario()
    {
        return $this->endereco_usuario;
    }

    public function setIdUsuario($id)
    {
        $this->id_usuario = $id;
    }
    public function setEmailUsuario($email)
    {
        $this->email_usuario = $email;
    }
    public function setSenhaUsuarioHash($senha_hash)
    {
        $this->senha_usuario_hash = $senha_hash;
    }
    public function setNomeUsuario($nome)
    {
        $this->nome_usuario = $nome;
    }
    public function setTelefoneUsuario($telefone)
    {
        $this->telefone_usuario = $telefone;
    }
    public function setCpfUsuario($cpf)
    {
        $this->cpf_usuario = $cpf;
    }
    public function setTipoUsuario($tipo)
    {
        $this->tipo_usuario = $tipo;
    }
    public function setEstadoUsuario($estado)
    {
        $this->estado_usuario = $estado;
    }
    public function setCidadeUsuario($cidade)
    {
        $this->cidade_usuario = $cidade;
    }
    public function setEnderecoUsuario($endereco)
    {
        $this->endereco_usuario = $endereco;
    }
}
?>