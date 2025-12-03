<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ . "/UsuarioPlano.php";

class UsuarioPlanoDAO
{
    public $conn;

    public function __construct()
    {
        $this->conn = Conexao::getInstance();
        $this->criarTabela();
    }

    private function criarTabela()
    {
        // Garante que as tabelas dependentes existam
        $sql = "CREATE TABLE IF NOT EXISTS Planos (
            id_plano INT AUTO_INCREMENT PRIMARY KEY,
            preco_plano DECIMAL(5,2) NOT NULL,
            descricao_plano VARCHAR(500)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->exec($sql);

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
        $this->conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS Usuario_Plano (
            id_plano INT NOT NULL,
            id_usuario INT NOT NULL,
            data_inicio_plano DATETIME NOT NULL,
            data_fim_plano DATETIME,
            PRIMARY KEY (id_plano, id_usuario),
            FOREIGN KEY (id_plano) REFERENCES Planos (id_plano) ON DELETE CASCADE,
            FOREIGN KEY (id_usuario) REFERENCES Usuarios (id_usuario) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Usuario_Plano: " . $e->getMessage());
        }
    }

    public function cadastrar(UsuarioPlano $up)
    {
        $sql = "INSERT INTO Usuario_Plano(id_plano, id_usuario, data_inicio_plano, data_fim_plano) VALUES (?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $up->getIdPlano(),
            $up->getIdUsuario(),
            $up->getDataInicio(),
            $up->getDataFim()
        ]);
    }

    public function readByUsuario($idUsuario)
    {
        $planos = [];
        $sql = 'SELECT * FROM Usuario_Plano WHERE id_usuario=? ORDER BY data_inicio_plano DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idUsuario]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $planos[] = new UsuarioPlano(
                $row['id_plano'],
                $row['id_usuario'],
                $row['data_inicio_plano'],
                $row['data_fim_plano']
            );
        }
        return $planos;
    }

    public function readByPlano($idPlano)
    {
        $usuarios = [];
        $sql = 'SELECT * FROM Usuario_Plano WHERE id_plano=? ORDER BY data_inicio_plano';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idPlano]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new UsuarioPlano(
                $row['id_plano'],
                $row['id_usuario'],
                $row['data_inicio_plano'],
                $row['data_fim_plano']
            );
        }
        return $usuarios;
    }

    public function update(UsuarioPlano $up)
    {
        $sql = "UPDATE Usuario_Plano SET data_inicio_plano=?, data_fim_plano=? WHERE id_plano=? AND id_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $up->getDataInicio(),
            $up->getDataFim(),
            $up->getIdPlano(),
            $up->getIdUsuario()
        ]);
    }

    public function delete($idPlano, $idUsuario)
    {
        $sql = "DELETE FROM Usuario_Plano WHERE id_plano=? AND id_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idPlano, $idUsuario]);
    }

    public function temPlanoAtivo($idUsuario)
    {
        $sql = "SELECT * FROM Usuario_Plano 
                WHERE id_usuario=? 
                AND (data_fim_plano IS NULL OR data_fim_plano > NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function getPlanoAtivo($idUsuario)
    {
        $sql = "SELECT up.*, p.preco_plano, p.descricao_plano 
                FROM Usuario_Plano up
                INNER JOIN Planos p ON up.id_plano = p.id_plano
                WHERE up.id_usuario=? 
                AND (up.data_fim_plano IS NULL OR up.data_fim_plano > NOW())
                ORDER BY up.data_inicio_plano DESC
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cancelarPlanoAtivo($idUsuario)
    {
        $sql = "UPDATE Usuario_Plano SET data_fim_plano = NOW() WHERE id_usuario = ? AND (data_fim_plano IS NULL OR data_fim_plano > NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idUsuario]);
    }
}

