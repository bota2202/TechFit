<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ . "/Turma.php";

class TurmaDAO
{
    public $conn;

    public function __construct()
    {
        $this->conn = Conexao::getInstance();
        $this->criarTabela();
    }

    private function criarTabela()
    {
        // Primeiro verifica se Cursos existe
        $sql = "CREATE TABLE IF NOT EXISTS Cursos (
            id_curso INT AUTO_INCREMENT PRIMARY KEY,
            nome_curso VARCHAR(255) NOT NULL,
            tipo_curso VARCHAR(100) NOT NULL,
            descricao_curso VARCHAR(500),
            preco_curso DECIMAL(5,2) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->exec($sql);

        // Verifica se Usuarios existe
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

        $sql = "CREATE TABLE IF NOT EXISTS Turmas (
            id_turma INT AUTO_INCREMENT PRIMARY KEY,
            id_curso INT NOT NULL,
            responsavel_turma INT NOT NULL,
            nome_turma VARCHAR(255) NOT NULL,
            data_inicio DATETIME NOT NULL,
            data_fim DATETIME NOT NULL,
            horario_turma VARCHAR(100),
            capacidade_maxima INT DEFAULT 20,
            ativa BOOLEAN DEFAULT TRUE,
            FOREIGN KEY (id_curso) REFERENCES Cursos (id_curso) ON DELETE CASCADE,
            FOREIGN KEY (responsavel_turma) REFERENCES Usuarios (id_usuario) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Turmas: " . $e->getMessage());
        }
    }

    public function cadastrar(Turma $t)
    {
        $sql = "INSERT INTO Turmas(id_curso, responsavel_turma, nome_turma, data_inicio, data_fim, horario_turma, capacidade_maxima) 
                VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $t->getIdCurso(),
            $t->getResponsavel(),
            $t->getNome(),
            $t->getDataInicio(),
            $t->getDataFim(),
            $t->getHorario(),
            $t->getCapacidadeMaxima() ?: 20
        ]);
        return $this->conn->lastInsertId();
    }

    public function readAll()
    {
        $turmas = [];
        $sql = 'SELECT * FROM Turmas ORDER BY data_inicio';
        $stmt = $this->conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $turmas[] = new Turma(
                $row['id_turma'],
                $row['id_curso'],
                $row['responsavel_turma'],
                $row['nome_turma'],
                $row['data_inicio'],
                $row['data_fim'],
                $row['horario_turma'],
                $row['capacidade_maxima'] ?? 20,
                $row['ativa'] ?? true
            );
        }
        return $turmas;
    }

    public function readById($id)
    {
        $sql = 'SELECT * FROM Turmas WHERE id_turma=?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Turma(
                $row['id_turma'],
                $row['id_curso'],
                $row['responsavel_turma'],
                $row['nome_turma'],
                $row['data_inicio'],
                $row['data_fim'],
                $row['horario_turma'],
                $row['capacidade_maxima'] ?? 20,
                $row['ativa'] ?? true
            );
        }
        return null;
    }

    public function readByCurso($idCurso)
    {
        $turmas = [];
        $sql = 'SELECT * FROM Turmas WHERE id_curso=? ORDER BY data_inicio';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idCurso]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $turmas[] = new Turma(
                $row['id_turma'],
                $row['id_curso'],
                $row['responsavel_turma'],
                $row['nome_turma'],
                $row['data_inicio'],
                $row['data_fim'],
                $row['horario_turma'],
                $row['capacidade_maxima'] ?? 20,
                $row['ativa'] ?? true
            );
        }
        return $turmas;
    }

    public function update(Turma $t)
    {
        $sql = "UPDATE Turmas SET id_curso=?, responsavel_turma=?, nome_turma=?, data_inicio=?, data_fim=?, horario_turma=?, capacidade_maxima=? 
                WHERE id_turma=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $t->getIdCurso(),
            $t->getResponsavel(),
            $t->getNome(),
            $t->getDataInicio(),
            $t->getDataFim(),
            $t->getHorario(),
            $t->getCapacidadeMaxima() ?: 20,
            $t->getId()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM Turmas WHERE id_turma=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }
}

