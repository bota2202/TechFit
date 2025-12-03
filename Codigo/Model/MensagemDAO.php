<?php

require_once __DIR__ . '/Conexao.php';
require_once __DIR__ . '/Mensagem.php';

class MensagemDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexao::getInstance();
        $this->createTable();
    }

    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Mensagens (
            id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
            id_remetente INT NOT NULL,
            id_destinatario INT,
            assunto VARCHAR(255) NOT NULL,
            conteudo TEXT NOT NULL,
            data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
            lida BOOLEAN DEFAULT FALSE,
            tipo VARCHAR(50) DEFAULT 'geral',
            id_turma INT,
            FOREIGN KEY (id_remetente) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
            FOREIGN KEY (id_destinatario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
            FOREIGN KEY (id_turma) REFERENCES Turmas(id_turma) ON DELETE CASCADE
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Erro ao criar tabela Mensagens: " . $e->getMessage());
        }
    }

    public function cadastrar(Mensagem $mensagem)
    {
        $sql = "INSERT INTO Mensagens (id_remetente, id_destinatario, assunto, conteudo, tipo, id_turma) 
                VALUES (:id_remetente, :id_destinatario, :assunto, :conteudo, :tipo, :id_turma)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_remetente' => $mensagem->getIdRemetente(),
            ':id_destinatario' => $mensagem->getIdDestinatario(),
            ':assunto' => $mensagem->getAssunto(),
            ':conteudo' => $mensagem->getConteudo(),
            ':tipo' => $mensagem->getTipo(),
            ':id_turma' => $mensagem->getIdTurma()
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function readByDestinatario($idDestinatario, $lida = null)
    {
        $sql = "SELECT * FROM Mensagens WHERE id_destinatario = :id_destinatario";
        if ($lida !== null) {
            $sql .= " AND lida = " . ($lida ? 'TRUE' : 'FALSE');
        }
        $sql .= " ORDER BY data_envio DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_destinatario' => $idDestinatario]);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Mensagem(
                $row['id_mensagem'],
                $row['id_remetente'],
                $row['id_destinatario'],
                $row['assunto'],
                $row['conteudo'],
                $row['data_envio'],
                $row['lida'],
                $row['tipo'],
                $row['id_turma']
            );
        }
        
        return $result;
    }

    public function readByTurma($idTurma)
    {
        $sql = "SELECT * FROM Mensagens WHERE id_turma = :id_turma ORDER BY data_envio DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_turma' => $idTurma]);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Mensagem(
                $row['id_mensagem'],
                $row['id_remetente'],
                $row['id_destinatario'],
                $row['assunto'],
                $row['conteudo'],
                $row['data_envio'],
                $row['lida'],
                $row['tipo'],
                $row['id_turma']
            );
        }
        
        return $result;
    }

    public function marcarComoLida($id)
    {
        $sql = "UPDATE Mensagens SET lida = TRUE WHERE id_mensagem = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM Mensagens WHERE id_mensagem = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function readConversa($idUsuario1, $idUsuario2)
    {
        $sql = "SELECT * FROM Mensagens 
                WHERE ((id_remetente = :id1 AND id_destinatario = :id2) 
                   OR (id_remetente = :id3 AND id_destinatario = :id4))
                AND (tipo != 'turma' OR tipo IS NULL)
                ORDER BY data_envio ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id1' => $idUsuario1,
            ':id2' => $idUsuario2,
            ':id3' => $idUsuario2,
            ':id4' => $idUsuario1
        ]);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Mensagem(
                $row['id_mensagem'],
                $row['id_remetente'],
                $row['id_destinatario'],
                $row['assunto'],
                $row['conteudo'],
                $row['data_envio'],
                $row['lida'],
                $row['tipo'],
                $row['id_turma']
            );
        }
        
        return $result;
    }

    public function readConversas($idUsuario)
    {
        $sql = "SELECT 
                    outro_usuario,
                    MAX(data_envio) as ultima_mensagem,
                    MIN(assunto) as titulo_conversa
                FROM (
                    SELECT 
                        CASE 
                            WHEN id_remetente = :id_usuario1 THEN id_destinatario 
                            ELSE id_remetente 
                        END as outro_usuario,
                        data_envio,
                        assunto
                    FROM Mensagens 
                    WHERE (id_remetente = :id_usuario2 OR id_destinatario = :id_usuario3)
                      AND tipo != 'turma'
                      AND id_destinatario IS NOT NULL
                ) as conversas
                GROUP BY outro_usuario
                ORDER BY ultima_mensagem DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario1' => $idUsuario,
            ':id_usuario2' => $idUsuario,
            ':id_usuario3' => $idUsuario
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTituloConversa($idUsuario1, $idUsuario2)
    {
        $sql = "SELECT assunto FROM Mensagens 
                WHERE ((id_remetente = :id1 AND id_destinatario = :id2) 
                   OR (id_remetente = :id3 AND id_destinatario = :id4))
                AND tipo != 'turma'
                ORDER BY data_envio ASC LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id1' => $idUsuario1,
            ':id2' => $idUsuario2,
            ':id3' => $idUsuario2,
            ':id4' => $idUsuario1
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['assunto'] : 'Nova conversa';
    }
}

