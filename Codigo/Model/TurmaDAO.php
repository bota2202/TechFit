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
        
        // Migração: Adiciona colunas se não existirem (para tabelas antigas)
        $this->migrarTabelaTurmas();
        
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

    public function cadastrar(Turma $t, $horaInicio = null, $diasSemana = null, $duracaoMinutos = null)
    {
        // Verifica quais colunas existem para usar a estrutura correta
        $columns = $this->conn->query("SHOW COLUMNS FROM Turmas")->fetchAll(PDO::FETCH_COLUMN);
        $temDataInicioPeriodo = in_array('data_inicio_periodo', $columns);
        $temDataFimPeriodo = in_array('data_fim_periodo', $columns);
        $temHoraInicio = in_array('hora_inicio', $columns);
        $temDiasSemana = in_array('dias_semana', $columns);
        $temDuracaoMinutos = in_array('duracao_minutos', $columns);
        
        $dataInicio = $t->getDataInicio();
        $dataFim = $t->getDataFim();
        
        // Converte DATETIME para DATE se necessário
        if ($dataInicio && strlen($dataInicio) > 10) {
            $dataInicio = substr($dataInicio, 0, 10);
        }
        if ($dataFim && strlen($dataFim) > 10) {
            $dataFim = substr($dataFim, 0, 10);
        }
        
        // Prepara valores para colunas opcionais
        $horaInicioValue = $horaInicio ?: '00:00:00';
        $diasSemanaValue = $diasSemana ? (is_array($diasSemana) ? implode(',', $diasSemana) : $diasSemana) : null;
        $duracaoMinutosValue = $duracaoMinutos ?: 60;
        
        if ($temDataInicioPeriodo && $temDataFimPeriodo) {
            // Monta a query dinamicamente baseada nas colunas disponíveis
            $campos = ['id_curso', 'responsavel_turma', 'nome_turma', 'data_inicio_periodo', 'data_fim_periodo', 'horario_turma', 'capacidade_maxima'];
            $valores = [$t->getIdCurso(), $t->getResponsavel(), $t->getNome(), $dataInicio, $dataFim, $t->getHorario(), $t->getCapacidadeMaxima() ?: 20];
            $placeholders = ['?', '?', '?', '?', '?', '?', '?'];
            
            if ($temHoraInicio) {
                $campos[] = 'hora_inicio';
                $valores[] = $horaInicioValue;
                $placeholders[] = '?';
            }
            if ($temDiasSemana && $diasSemanaValue !== null) {
                $campos[] = 'dias_semana';
                $valores[] = $diasSemanaValue;
                $placeholders[] = '?';
            }
            if ($temDuracaoMinutos) {
                $campos[] = 'duracao_minutos';
                $valores[] = $duracaoMinutosValue;
                $placeholders[] = '?';
            }
            
            $sql = "INSERT INTO Turmas(" . implode(', ', $campos) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($valores);
        } else {
            // Fallback para estrutura antiga
            $sql = "INSERT INTO Turmas(id_curso, responsavel_turma, nome_turma, data_inicio, data_fim, horario_turma, capacidade_maxima) 
                    VALUES (?,?,?,?,?,?,?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $t->getIdCurso(),
                $t->getResponsavel(),
                $t->getNome(),
                $dataInicio . ' 00:00:00',
                $dataFim . ' 23:59:59',
                $t->getHorario(),
                $t->getCapacidadeMaxima() ?: 20
            ]);
        }
        return $this->conn->lastInsertId();
    }

    public function readAll()
    {
        $turmas = [];
        try {
            // Verifica quais colunas existem
            $columns = $this->conn->query("SHOW COLUMNS FROM Turmas")->fetchAll(PDO::FETCH_COLUMN);
            $temDataInicioPeriodo = in_array('data_inicio_periodo', $columns);
            $temDataFimPeriodo = in_array('data_fim_periodo', $columns);
            $temDataInicio = in_array('data_inicio', $columns);
            $temDataFim = in_array('data_fim', $columns);
            
            // Monta a query baseada nas colunas disponíveis
            if ($temDataInicioPeriodo) {
                $orderBy = 'data_inicio_periodo';
            } elseif ($temDataInicio) {
                $orderBy = 'data_inicio';
            } else {
                $orderBy = 'id_turma'; // Fallback se não houver coluna de data
            }
            
            $sql = "SELECT * FROM Turmas ORDER BY $orderBy";
            $stmt = $this->conn->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dataInicio = $row['data_inicio_periodo'] ?? $row['data_inicio'] ?? null;
                $dataFim = $row['data_fim_periodo'] ?? $row['data_fim'] ?? null;
                $turmas[] = new Turma(
                    $row['id_turma'],
                    $row['id_curso'],
                    $row['responsavel_turma'],
                    $row['nome_turma'],
                    $dataInicio,
                    $dataFim,
                    $row['horario_turma'],
                    $row['capacidade_maxima'] ?? 20,
                    $row['ativa'] ?? true
                );
            }
        } catch (PDOException $e) {
            error_log("Erro ao ler todas as turmas: " . $e->getMessage());
            throw $e;
        }
        return $turmas;
    }

    public function readById($id)
    {
        $sql = 'SELECT * FROM Turmas WHERE id_turma=?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dataInicio = $row['data_inicio_periodo'] ?? $row['data_inicio'] ?? null;
            $dataFim = $row['data_fim_periodo'] ?? $row['data_fim'] ?? null;
            return new Turma(
                $row['id_turma'],
                $row['id_curso'],
                $row['responsavel_turma'],
                $row['nome_turma'],
                $dataInicio,
                $dataFim,
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
            // Verifica quais colunas existem
            $columns = $this->conn->query("SHOW COLUMNS FROM Turmas")->fetchAll(PDO::FETCH_COLUMN);
            $temDataInicioPeriodo = in_array('data_inicio_periodo', $columns);
            $temDataFimPeriodo = in_array('data_fim_periodo', $columns);
            $temDataInicio = in_array('data_inicio', $columns);
            $temDataFim = in_array('data_fim', $columns);
            
            // Monta a query baseada nas colunas disponíveis
            $colDataInicio = $temDataInicioPeriodo ? 'data_inicio_periodo' : ($temDataInicio ? 'data_inicio' : 'NULL');
            $colDataFim = $temDataFimPeriodo ? 'data_fim_periodo' : ($temDataFim ? 'data_fim' : 'NULL');
            
            if ($temDataInicioPeriodo && $temDataFimPeriodo) {
                $sql = 'SELECT id_turma, id_curso, responsavel_turma, nome_turma, 
                        data_inicio_periodo as data_inicio, 
                        data_fim_periodo as data_fim,
                        horario_turma, capacidade_maxima, ativa
                        FROM Turmas 
                        WHERE id_curso=? 
                        AND (ativa IS NULL OR ativa=1 OR ativa=TRUE) 
                        ORDER BY data_inicio_periodo';
            } elseif ($temDataInicio && $temDataFim) {
                $sql = 'SELECT id_turma, id_curso, responsavel_turma, nome_turma, 
                        DATE(data_inicio) as data_inicio, 
                        DATE(data_fim) as data_fim,
                        horario_turma, capacidade_maxima, ativa
                        FROM Turmas 
                        WHERE id_curso=? 
                        AND (ativa IS NULL OR ativa=1 OR ativa=TRUE) 
                        ORDER BY data_inicio';
            } else {
                // Usa COALESCE como fallback
                $sql = 'SELECT id_turma, id_curso, responsavel_turma, nome_turma, 
                        COALESCE(data_inicio_periodo, DATE(data_inicio)) as data_inicio, 
                        COALESCE(data_fim_periodo, DATE(data_fim)) as data_fim,
                        horario_turma, capacidade_maxima, ativa
                        FROM Turmas 
                        WHERE id_curso=? 
                        AND (ativa IS NULL OR ativa=1 OR ativa=TRUE) 
                        ORDER BY COALESCE(data_inicio_periodo, data_inicio)';
            }
            
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
        // Verifica quais colunas existem para usar a estrutura correta
        $columns = $this->conn->query("SHOW COLUMNS FROM Turmas")->fetchAll(PDO::FETCH_COLUMN);
        $temDataInicioPeriodo = in_array('data_inicio_periodo', $columns);
        $temDataFimPeriodo = in_array('data_fim_periodo', $columns);
        
        $dataInicio = $t->getDataInicio();
        $dataFim = $t->getDataFim();
        
        // Converte DATETIME para DATE se necessário
        if ($dataInicio && strlen($dataInicio) > 10) {
            $dataInicio = substr($dataInicio, 0, 10);
        }
        if ($dataFim && strlen($dataFim) > 10) {
            $dataFim = substr($dataFim, 0, 10);
        }
        
        if ($temDataInicioPeriodo && $temDataFimPeriodo) {
            $sql = "UPDATE Turmas SET id_curso=?, responsavel_turma=?, nome_turma=?, data_inicio_periodo=?, data_fim_periodo=?, horario_turma=?, capacidade_maxima=? 
                    WHERE id_turma=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $t->getIdCurso(),
                $t->getResponsavel(),
                $t->getNome(),
                $dataInicio,
                $dataFim,
                $t->getHorario(),
                $t->getCapacidadeMaxima() ?: 20,
                $t->getId()
            ]);
        } else {
            // Fallback para estrutura antiga
            $sql = "UPDATE Turmas SET id_curso=?, responsavel_turma=?, nome_turma=?, data_inicio=?, data_fim=?, horario_turma=?, capacidade_maxima=? 
                    WHERE id_turma=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $t->getIdCurso(),
                $t->getResponsavel(),
                $t->getNome(),
                $dataInicio . ' 00:00:00',
                $dataFim . ' 23:59:59',
                $t->getHorario(),
                $t->getCapacidadeMaxima() ?: 20,
                $t->getId()
            ]);
        }
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
    
    private function migrarTabelaTurmas()
    {
        try {
            // Verifica se a tabela existe
            $checkTable = $this->conn->query("SHOW TABLES LIKE 'Turmas'");
            if ($checkTable->rowCount() == 0) {
                return; // Tabela não existe, será criada pelo CREATE TABLE IF NOT EXISTS
            }
            
            // Verifica quais colunas existem
            $columns = $this->conn->query("SHOW COLUMNS FROM Turmas")->fetchAll(PDO::FETCH_COLUMN);
            
            $temDataInicioPeriodo = in_array('data_inicio_periodo', $columns);
            $temDataFimPeriodo = in_array('data_fim_periodo', $columns);
            $temDataInicio = in_array('data_inicio', $columns);
            $temDataFim = in_array('data_fim', $columns);
            $temHoraInicio = in_array('hora_inicio', $columns);
            $temDiasSemana = in_array('dias_semana', $columns);
            $temDuracaoMinutos = in_array('duracao_minutos', $columns);
            $temCapacidadeMaxima = in_array('capacidade_maxima', $columns);
            $temAtiva = in_array('ativa', $columns);
            
            // Se tem estrutura antiga (data_inicio/data_fim) mas não tem nova (data_inicio_periodo/data_fim_periodo)
            if (($temDataInicio || $temDataFim) && (!$temDataInicioPeriodo || !$temDataFimPeriodo)) {
                // Adiciona as novas colunas
                if (!$temDataInicioPeriodo) {
                    try {
                        if ($temDataInicio) {
                            // Migra dados de data_inicio para data_inicio_periodo
                            $this->conn->exec("ALTER TABLE Turmas ADD COLUMN data_inicio_periodo DATE");
                            $this->conn->exec("UPDATE Turmas SET data_inicio_periodo = DATE(data_inicio) WHERE data_inicio_periodo IS NULL");
                            $this->conn->exec("UPDATE Turmas SET data_inicio_periodo = CURDATE() WHERE data_inicio_periodo IS NULL");
                            $this->conn->exec("ALTER TABLE Turmas MODIFY COLUMN data_inicio_periodo DATE NOT NULL");
                        } else {
                            $this->conn->exec("ALTER TABLE Turmas ADD COLUMN data_inicio_periodo DATE");
                            $this->conn->exec("UPDATE Turmas SET data_inicio_periodo = CURDATE() WHERE data_inicio_periodo IS NULL");
                            $this->conn->exec("ALTER TABLE Turmas MODIFY COLUMN data_inicio_periodo DATE NOT NULL");
                        }
                    } catch (PDOException $e) {
                        error_log("Erro ao adicionar coluna data_inicio_periodo: " . $e->getMessage());
                    }
                }
                
                if (!$temDataFimPeriodo) {
                    try {
                        if ($temDataFim) {
                            // Migra dados de data_fim para data_fim_periodo
                            $this->conn->exec("ALTER TABLE Turmas ADD COLUMN data_fim_periodo DATE");
                            $this->conn->exec("UPDATE Turmas SET data_fim_periodo = DATE(data_fim) WHERE data_fim_periodo IS NULL");
                            $this->conn->exec("UPDATE Turmas SET data_fim_periodo = CURDATE() WHERE data_fim_periodo IS NULL");
                            $this->conn->exec("ALTER TABLE Turmas MODIFY COLUMN data_fim_periodo DATE NOT NULL");
                        } else {
                            $this->conn->exec("ALTER TABLE Turmas ADD COLUMN data_fim_periodo DATE");
                            $this->conn->exec("UPDATE Turmas SET data_fim_periodo = CURDATE() WHERE data_fim_periodo IS NULL");
                            $this->conn->exec("ALTER TABLE Turmas MODIFY COLUMN data_fim_periodo DATE NOT NULL");
                        }
                    } catch (PDOException $e) {
                        error_log("Erro ao adicionar coluna data_fim_periodo: " . $e->getMessage());
                    }
                }
            }
            
            // Adiciona hora_inicio se não existir (pode ser NULL)
            if (!$temHoraInicio) {
                try {
                    $this->conn->exec("ALTER TABLE Turmas ADD COLUMN hora_inicio TIME");
                } catch (PDOException $e) {
                    error_log("Erro ao adicionar coluna hora_inicio: " . $e->getMessage());
                }
            } else {
                // Se existe mas é NOT NULL, torna opcional
                try {
                    $this->conn->exec("ALTER TABLE Turmas MODIFY COLUMN hora_inicio TIME");
                } catch (PDOException $e) {
                    error_log("Erro ao modificar coluna hora_inicio: " . $e->getMessage());
                }
            }
            
            // Adiciona dias_semana se não existir (pode ser NULL)
            if (!$temDiasSemana) {
                try {
                    $this->conn->exec("ALTER TABLE Turmas ADD COLUMN dias_semana VARCHAR(50)");
                } catch (PDOException $e) {
                    error_log("Erro ao adicionar coluna dias_semana: " . $e->getMessage());
                }
            }
            
            // Adiciona duracao_minutos se não existir (pode ser NULL)
            if (!$temDuracaoMinutos) {
                try {
                    $this->conn->exec("ALTER TABLE Turmas ADD COLUMN duracao_minutos INT DEFAULT 60");
                } catch (PDOException $e) {
                    error_log("Erro ao adicionar coluna duracao_minutos: " . $e->getMessage());
                }
            }
            
            // Adiciona capacidade_maxima se não existir
            if (!$temCapacidadeMaxima) {
                try {
                    $this->conn->exec("ALTER TABLE Turmas ADD COLUMN capacidade_maxima INT DEFAULT 20");
                } catch (PDOException $e) {
                    error_log("Erro ao adicionar coluna capacidade_maxima: " . $e->getMessage());
                }
            }
            
            // Adiciona ativa se não existir
            if (!$temAtiva) {
                try {
                    $this->conn->exec("ALTER TABLE Turmas ADD COLUMN ativa BOOLEAN DEFAULT TRUE");
                } catch (PDOException $e) {
                    error_log("Erro ao adicionar coluna ativa: " . $e->getMessage());
                }
            }
        } catch (PDOException $e) {
            error_log("Erro ao migrar tabela Turmas: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Erro geral ao migrar tabela Turmas: " . $e->getMessage());
        }
    }
}

