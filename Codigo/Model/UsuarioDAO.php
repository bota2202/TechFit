<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ .  "/Usuario.php";
include_once __DIR__ . "/config.php";

class UsuarioDAO
{
    public $conn;

    public function __construct()
    {
        $this->conn = Conexao::getInstance();
        $this->criarTabela();
        $this->inserirUsuarioAdmin();
    }

    private function criarTabela()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Usuarios (
            id_usuario INT AUTO_INCREMENT PRIMARY KEY,
            email_usuario VARCHAR(255) NOT NULL UNIQUE,
            senha_usuario_hash VARCHAR(255) NOT NULL,
            nome_usuario VARCHAR(255) NOT NULL,
            telefone_usuario VARCHAR(15) NOT NULL,
            cpf_usuario VARCHAR(14) UNIQUE NOT NULL,
            tipo_usuario TINYINT NOT NULL DEFAULT 3,
            cidade_usuario VARCHAR(255),
            estado_usuario VARCHAR(255),
            bairro_usuario VARCHAR(255),
            rua_usuario VARCHAR(255)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Usuarios: " . $e->getMessage());
        }
    }

    private function inserirUsuarioAdmin()
    {
        try {
            // Verifica se o admin já existe
            $sql = "SELECT id_usuario FROM usuarios WHERE email_usuario = 'admin@admin' OR id_usuario = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existe) {
                $senhaHash = password_hash('admin', PASSWORD_DEFAULT);
                
                $tipoAdmin = TIPO_USUARIO_ADMIN;
                $sql = "INSERT INTO usuarios (
                    id_usuario,
                    email_usuario,
                    senha_usuario_hash,
                    nome_usuario,
                    telefone_usuario,
                    cpf_usuario,
                    tipo_usuario,
                    cidade_usuario,
                    estado_usuario,
                    bairro_usuario,
                    rua_usuario
                ) VALUES (
                    1,
                    'admin@admin',
                    ?,
                    'admin',
                    '00000000000',
                    '00000000000',
                    ?,
                    NULL,
                    NULL,
                    NULL,
                    NULL
                )";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$senhaHash, $tipoAdmin]);
            }
        } catch(PDOException $e) {
            error_log("Erro ao inserir usuário admin: " . $e->getMessage());
        }
    }

    public function cadastrar(Usuario $u)
    {
        $sql = "INSERT INTO usuarios(nome_usuario,email_usuario,senha_usuario_hash,telefone_usuario,cpf_usuario,cidade_usuario,estado_usuario,bairro_usuario,rua_usuario) VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getNome(),
            $u->getEmail(),
            $u->getSenha(),
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

    public function readByEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readByCPF($cpf)
    {
        $sql = "SELECT * FROM usuarios WHERE cpf_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$cpf]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readById($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Usuario(
                $row['id_usuario'],
                $row['nome_usuario'],
                $row['email_usuario'],
                null,
                $row['telefone_usuario'],
                $row['cpf_usuario'],
                $row['tipo_usuario'] ?? TIPO_USUARIO_ALUNO,
                $row['cidade_usuario'],
                $row['estado_usuario'],
                $row['bairro_usuario'],
                $row['rua_usuario']
            );
        }
        return null;
    }


    public function update(Usuario $u)
    {
        if ($u->getSenha() && !empty(trim($u->getSenha()))) {
            $sql = "UPDATE usuarios SET nome_usuario=?, email_usuario=?, telefone_usuario=?, cpf_usuario=?, tipo_usuario=?, cidade_usuario=?, estado_usuario=?, bairro_usuario=?, rua_usuario=?, senha_usuario_hash=? WHERE id_usuario=?";
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
        } else {
            $sql = "UPDATE usuarios SET nome_usuario=?, email_usuario=?, telefone_usuario=?, cpf_usuario=?, tipo_usuario=?, cidade_usuario=?, estado_usuario=?, bairro_usuario=?, rua_usuario=? WHERE id_usuario=?";
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
                $u->getId()
            ]);
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM usuarios WHERE id_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }
}
