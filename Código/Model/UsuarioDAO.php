<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ .  "/Usuario.php";

class UsuarioDAO
{
    public $conn;

    public function __construct()
    {
        $this->conn = Conexao::getInstance();
    }

    public function cadastrar(Usuario $u)
    {
        $sql = "INSERT INTO usuarios(nome_usuario,email_usuario,senha_usuario_hash,telefone_usuario,cpf_usuario,cidade_usuario,estado_usuario,bairro_usuario,rua_usuario) VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getNome(),
            $u->getEmail(),
            password_hash($u->getSenha(), PASSWORD_DEFAULT),
            $u->getTelefone(),
            $u->getCpf(),
            $u->getCidade(),
            $u->getEstado(),
            $u->getBairro(),
            $u->getRua()
        ]);
    }

    public function readAll()
    {
        $usuarios = [];
        $sql = 'SELECT * FROM usuarios';
        $stmt = $this->conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario(
                $row['id_usuario'],
                $row['nome_usuario'],
                $row['email_usuario'],
                null,
                $row['telefone_usuario'],
                $row['cpf_usuario'],
                $row['tipo_usuario'],
                $row['cidade_usuario'],
                $row['estado_usuario'],
                $row['bairro_usuario'],
                $row['rua_usuario']
            );
        }
        return $usuarios;
    }

    public function readById($id)
    {
        $sql = 'SELECT * FROM usuarios WHERE id_usuario=?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Usuario(
                $row['id_usuario'],
                $row['nome_usuario'],
                $row['email_usuario'],
                null,
                $row['telefone_usuario'],
                $row['cpf_usuario'],
                $row['tipo_usuario'],
                $row['cidade_usuario'],
                $row['estado_usuario'],
                $row['bairro_usuario'],
                $row['rua_usuario']
            );
        } else {
            return null;
        }
    }

    public function readByEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function update(Usuario $u)
    {
        $sql = "UPDATE usuarios SET nome_usuario=?,email_usuario=?,  telefone_usuario=?, cpf_usuario=?, tipo_usuario=?, cidade_usuario=?,estado_usuario=?,bairro_usuario=?,rua_usuario=?, senha_usuario_hash=? WHERE id_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getNome(),
            $u->getEmail(),
            $u->getTelefone(),
            $u->getCpf(),
            $u->getTipo(),
            $u->getCidade(),
            $u->getEstado(),
            $u->getBairro(),
            $u->getRua(),
            password_hash($u->getSenha(), PASSWORD_DEFAULT),
            $u->getId()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM usuarios WHERE id_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }
}
