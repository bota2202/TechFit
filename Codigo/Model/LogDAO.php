<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ . "/Log.php";

class LogDAO
{
    public $conn;

    public function __construct()
    {
        $this->conn = Conexao::getInstance();
        $this->criarTabela();
    }

    private function criarTabela()
    {
        // Garante que a tabela Usuarios existe
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

        $sql = "CREATE TABLE IF NOT EXISTS Logs (
            id_log INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            acao VARCHAR(255) NOT NULL,
            data_acao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Logs: " . $e->getMessage());
        }
    }

    public function cadastrar(Log $log)
    {
        $sql = "INSERT INTO Logs(id_usuario, acao, data_acao) VALUES (?,?,NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$log->getIdUsuario(), $log->getAcao()]);
        return $this->conn->lastInsertId();
    }

    public function readAll()
    {
        $logs = [];
        $sql = 'SELECT * FROM Logs ORDER BY data_acao DESC LIMIT 1000';
        $stmt = $this->conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $logs[] = new Log(
                $row['id_log'],
                $row['id_usuario'],
                $row['acao'],
                $row['data_acao']
            );
        }
        return $logs;
    }

    public function readByUsuario($idUsuario)
    {
        $logs = [];
        $sql = 'SELECT * FROM Logs WHERE id_usuario=? ORDER BY data_acao DESC LIMIT 100';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idUsuario]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $logs[] = new Log(
                $row['id_log'],
                $row['id_usuario'],
                $row['acao'],
                $row['data_acao']
            );
        }
        return $logs;
    }
}

