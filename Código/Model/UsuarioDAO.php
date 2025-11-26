<?php

include_once __DIR__ . "/Usuario.php";
include_once __DIR__ . "/Conexao.php";

class UsuarioDAO
{
    private $conn;

    public function __construct()
    {
        $this->conn = Conexao::getInstance();

        $sql = "CREATE TABLE IF NOT EXISTS Usuarios (
                id_usuario INT AUTO_INCREMENT PRIMARY KEY,
                email_usuario VARCHAR(255) NOT NULL UNIQUE,
                senha_usuario_hash VARCHAR(255) NOT NULL,
                nome_usuario VARCHAR(255) NOT NULL,
                telefone_usuario VARCHAR(14) NOT NULL,
                cpf_usuario VARCHAR(14) UNIQUE NOT NULL,
                tipo_usuario TINYINT NOT NULL DEFAULT 3,
                estado_usuario varchar(100),
                cidade_usuario varchar(100),
                endereco_usuario varchar(255)
            )";

        $stmt = $this->conn->exec($sql);
    }

    public function create(Usuario $u)
    {
        $sql = "INSERT INTO Usuarios(
                email_usuario,
                senha_usuario_hash,
                nome_usuario,
                telefone_usuario,
                cpf_usuario,
                estado_usuario,
                cidade_usuario,
                endereco_usuario
            ) VALUES (?,?,?,?,?,?,?,?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getEmailUsuario(),
            $u->getSenhaUsuarioHash(),
            $u->getNomeUsuario(),
            $u->getTelefoneUsuario(),
            $u->getCpfUsuario(),
            $u->getEstadoUsuario(),
            $u->getCidadeUsuario(),
            $u->getEnderecoUsuario()
        ]);

        $u->setIdUsuario($this->conn->lastInsertId());
    }
}
