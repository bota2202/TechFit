<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ . "/Presenca.php";

class PresencaDAO
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
        $sql = "CREATE TABLE IF NOT EXISTS Turmas (
            id_turma INT AUTO_INCREMENT PRIMARY KEY,
            id_curso INT NOT NULL,
            responsavel_turma INT NOT NULL,
            nome_turma VARCHAR(255) NOT NULL,
            data_inicio DATETIME NOT NULL,
            data_fim DATETIME NOT NULL,
            horario_turma VARCHAR(100)
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

        $sql = "CREATE TABLE IF NOT EXISTS Presencas (
            id_presenca INT AUTO_INCREMENT PRIMARY KEY,
            id_turma INT NOT NULL,
            id_usuario INT NOT NULL,
            data_aula DATE NOT NULL,
            presente BOOLEAN NOT NULL DEFAULT TRUE,
            FOREIGN KEY (id_turma) REFERENCES Turmas(id_turma) ON DELETE CASCADE,
            FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
            UNIQUE KEY unique_presenca (id_turma, id_usuario, data_aula)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Presencas: " . $e->getMessage());
        }
    }

    public function cadastrar(Presenca $p)
    {
        $sql = "INSERT INTO Presencas(id_turma, id_usuario, data_aula, presente) VALUES (?,?,?,?)
                ON DUPLICATE KEY UPDATE presente=?";
        $stmt = $this->conn->prepare($sql);
        $presente = $p->getPresente() ? 1 : 0;
        $stmt->execute([
            $p->getIdTurma(),
            $p->getIdUsuario(),
            $p->getDataAula(),
            $presente,
            $presente
        ]);
        return $this->conn->lastInsertId();
    }

    public function readByTurma($idTurma, $dataAula = null)
    {
        $presencas = [];
        if ($dataAula) {
            $sql = 'SELECT * FROM Presencas WHERE id_turma=? AND data_aula=? ORDER BY id_usuario';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idTurma, $dataAula]);
        } else {
            $sql = 'SELECT * FROM Presencas WHERE id_turma=? ORDER BY data_aula DESC, id_usuario';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idTurma]);
        }
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $presencas[] = new Presenca(
                $row['id_presenca'],
                $row['id_turma'],
                $row['id_usuario'],
                $row['data_aula'],
                (bool)$row['presente']
            );
        }
        return $presencas;
    }

    public function readByUsuario($idUsuario)
    {
        $presencas = [];
        $sql = 'SELECT * FROM Presencas WHERE id_usuario=? ORDER BY data_aula DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idUsuario]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $presencas[] = new Presenca(
                $row['id_presenca'],
                $row['id_turma'],
                $row['id_usuario'],
                $row['data_aula'],
                (bool)$row['presente']
            );
        }
        return $presencas;
    }
}

