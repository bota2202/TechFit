<?php
/**
 * DAO Mensagem - TechFit
 */

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

    /**
     * Busca conversa entre dois usuários
     */
    public function readConversa($idUsuario1, $idUsuario2)
    {
        $sql = "SELECT * FROM Mensagens 
                WHERE ((id_remetente = :id1 AND id_destinatario = :id2) 
                   OR (id_remetente = :id2 AND id_destinatario = :id1))
                AND (tipo != 'turma' OR tipo IS NULL)
                ORDER BY data_envio ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id1' => $idUsuario1,
            ':id2' => $idUsuario2
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

    /**
     * Busca lista de conversas do usuário
     */
    public function readConversas($idUsuario)
    {
        $sql = "SELECT DISTINCT 
                    CASE 
                        WHEN id_remetente = :id_usuario THEN id_destinatario 
                        ELSE id_remetente 
                    END as outro_usuario,
                    MAX(data_envio) as ultima_mensagem
                FROM Mensagens 
                WHERE (id_remetente = :id_usuario OR id_destinatario = :id_usuario)
                  AND tipo != 'turma'
                  AND id_destinatario IS NOT NULL
                GROUP BY outro_usuario
                ORDER BY ultima_mensagem DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

