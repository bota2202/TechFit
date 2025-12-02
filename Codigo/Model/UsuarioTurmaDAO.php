<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ . "/UsuarioTurma.php";

class UsuarioTurmaDAO
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

        $sql = "CREATE TABLE IF NOT EXISTS Usuario_Turma (
            id_turma INT NOT NULL,
            id_usuario INT NOT NULL,
            data_matricula DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_turma, id_usuario),
            FOREIGN KEY (id_turma) REFERENCES Turmas (id_turma) ON DELETE CASCADE,
            FOREIGN KEY (id_usuario) REFERENCES Usuarios (id_usuario) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Usuario_Turma: " . $e->getMessage());
        }
    }

    public function cadastrar(UsuarioTurma $ut)
    {
        $sql = "INSERT INTO Usuario_Turma(id_turma, id_usuario, data_matricula) VALUES (?,?,NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$ut->getIdTurma(), $ut->getIdUsuario()]);
    }

    public function readByUsuario($idUsuario)
    {
        $matriculas = [];
        $sql = 'SELECT * FROM Usuario_Turma WHERE id_usuario=? ORDER BY data_matricula DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idUsuario]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $matriculas[] = new UsuarioTurma(
                $row['id_turma'],
                $row['id_usuario'],
                $row['data_matricula']
            );
        }
        return $matriculas;
    }

    public function readByTurma($idTurma)
    {
        $matriculas = [];
        $sql = 'SELECT * FROM Usuario_Turma WHERE id_turma=? ORDER BY data_matricula';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idTurma]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $matriculas[] = new UsuarioTurma(
                $row['id_turma'],
                $row['id_usuario'],
                $row['data_matricula']
            );
        }
        return $matriculas;
    }

    public function delete($idTurma, $idUsuario)
    {
        $sql = "DELETE FROM Usuario_Turma WHERE id_turma=? AND id_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idTurma, $idUsuario]);
    }
}

