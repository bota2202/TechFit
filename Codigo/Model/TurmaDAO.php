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
            data_inicio_periodo DATE NOT NULL,
            data_fim_periodo DATE NOT NULL,
            horario_turma VARCHAR(100),
            dias_semana VARCHAR(50),
            hora_inicio TIME NOT NULL,
            duracao_minutos INT DEFAULT 60,
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
        
        $sql = "CREATE TABLE IF NOT EXISTS Aulas (
            id_aula INT AUTO_INCREMENT PRIMARY KEY,
            id_turma INT NOT NULL,
            data_aula DATETIME NOT NULL,
            data_fim_aula DATETIME NOT NULL,
            FOREIGN KEY (id_turma) REFERENCES Turmas (id_turma) ON DELETE CASCADE,
            INDEX idx_data_aula (data_aula)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Aulas: " . $e->getMessage());
        }
    }

    public function cadastrar(Turma $t)
    {
        $sql = "INSERT INTO Turmas(id_curso, responsavel_turma, nome_turma, data_inicio, data_fim, horario_turma, capacidade_maxima) 
                VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $dataInicio = $t->getDataInicio();
        $dataFim = $t->getDataFim();
        $stmt->execute([
            $t->getIdCurso(),
            $t->getResponsavel(),
            $t->getNome(),
            $dataInicio,
            $dataFim,
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
        try {
            $sql = 'SELECT id_turma, id_curso, responsavel_turma, nome_turma, 
                    data_inicio, data_fim,
                    horario_turma, capacidade_maxima, ativa
                    FROM Turmas 
                    WHERE id_curso=? 
                    AND (ativa IS NULL OR ativa=1 OR ativa=TRUE) 
                    ORDER BY data_inicio';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCurso]);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!$row || !isset($row['id_turma'])) {
                    continue;
                }
                
                $dataInicio = $row['data_inicio'] ?? null;
                $dataFim = $row['data_fim'] ?? null;
                
                if (!$dataInicio || $dataInicio == '0000-00-00' || (is_string($dataInicio) && strpos($dataInicio, '0000') === 0)) {
                    $dataInicio = date('Y-m-d');
                } else {
                    if (is_string($dataInicio)) {
                        if (strlen($dataInicio) > 10) {
                            $dataInicio = substr($dataInicio, 0, 10);
                        }
                    }
                }
                
                if (!$dataFim || $dataFim == '0000-00-00' || (is_string($dataFim) && strpos($dataFim, '0000') === 0)) {
                    $dataFim = date('Y-m-d', strtotime('+1 month'));
                } else {
                    if (is_string($dataFim)) {
                        if (strlen($dataFim) > 10) {
                            $dataFim = substr($dataFim, 0, 10);
                        }
                    }
                }
                
                try {
                    $turma = new Turma(
                        (int)$row['id_turma'],
                        (int)$row['id_curso'],
                        (int)$row['responsavel_turma'],
                        $row['nome_turma'] ?? 'Turma sem nome',
                        $dataInicio,
                        $dataFim,
                        $row['horario_turma'] ?? null,
                        isset($row['capacidade_maxima']) ? (int)$row['capacidade_maxima'] : 20,
                        isset($row['ativa']) ? (bool)$row['ativa'] : true
                    );
                    $turmas[] = $turma;
                } catch (Exception $e) {
                    error_log("Erro ao criar objeto Turma para ID {$row['id_turma']}: " . $e->getMessage());
                    continue;
                }
            }
        } catch (PDOException $e) {
            error_log("Erro SQL ao buscar turmas por curso ID $idCurso: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
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
    
    public function verificarConflito($dataInicio, $dataFim, $idTurmaExcluir = null)
    {
        return $this->verificarConflitoAula($dataInicio, $dataFim, null);
    }
    
    
    public function getTurmasPorPeriodo($dataInicio, $dataFim)
    {
        $sql = "SELECT a.data_aula as data_inicio, a.data_fim_aula as data_fim, 
                t.nome_turma, t.id_turma, c.nome_curso, u.nome_usuario as nome_instrutor
                FROM Aulas a
                INNER JOIN Turmas t ON a.id_turma = t.id_turma
                LEFT JOIN Cursos c ON t.id_curso = c.id_curso
                LEFT JOIN Usuarios u ON t.responsavel_turma = u.id_usuario
                WHERE t.ativa = TRUE 
                AND a.data_aula >= ? 
                AND a.data_aula <= ?
                ORDER BY a.data_aula";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$dataInicio, $dataFim]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function cadastrarAula($idTurma, $dataAula, $dataFimAula)
    {
        $sql = "INSERT INTO Aulas(id_turma, data_aula, data_fim_aula) VALUES (?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idTurma, $dataAula, $dataFimAula]);
        return $this->conn->lastInsertId();
    }
    
    public function verificarConflitoAula($dataInicio, $dataFim, $idAulaExcluir = null)
    {
        $sql = "SELECT a.*, t.nome_turma 
                FROM Aulas a
                INNER JOIN Turmas t ON a.id_turma = t.id_turma
                WHERE t.ativa = TRUE 
                AND (
                    (a.data_aula < ? AND a.data_fim_aula > ?) OR
                    (a.data_aula < ? AND a.data_fim_aula > ?) OR
                    (a.data_aula >= ? AND a.data_fim_aula <= ?)
                )";
        
        $params = [$dataFim, $dataInicio, $dataFim, $dataInicio, $dataInicio, $dataFim];
        
        if ($idAulaExcluir) {
            $sql .= " AND a.id_aula != ?";
            $params[] = $idAulaExcluir;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getHorariosOcupadosPorData($data)
    {
        $sql = "SELECT a.data_aula as data_inicio, a.data_fim_aula as data_fim, t.nome_turma 
                FROM Aulas a
                INNER JOIN Turmas t ON a.id_turma = t.id_turma
                WHERE t.ativa = TRUE 
                AND DATE(a.data_aula) = ? 
                ORDER BY a.data_aula";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$data]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

