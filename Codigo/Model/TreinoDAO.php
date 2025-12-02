<?php
/**
 * DAO Treino - TechFit
 */

require_once __DIR__ . '/Conexao.php';
require_once __DIR__ . '/Treino.php';

class TreinoDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexao::getInstance();
        $this->createTable();
    }

    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Treinos (
            id_treino INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            titulo_treino VARCHAR(255) NOT NULL,
            descricao_treino TEXT NOT NULL,
            observacoes_treino TEXT,
            data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Erro ao criar tabela Treinos: " . $e->getMessage());
        }
    }

    public function cadastrar(Treino $treino)
    {
        $sql = "INSERT INTO Treinos (id_usuario, titulo_treino, descricao_treino, observacoes_treino) 
                VALUES (:id_usuario, :titulo, :descricao, :observacoes)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $treino->getIdUsuario(),
            ':titulo' => $treino->getTitulo(),
            ':descricao' => $treino->getDescricao(),
            ':observacoes' => $treino->getObservacoes()
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function readByUsuarioId($idUsuario)
    {
        $sql = "SELECT * FROM Treinos WHERE id_usuario = :id_usuario ORDER BY data_criacao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = [
                'id' => $row['id_treino'],
                'titulo' => $row['titulo_treino'],
                'descricao' => $row['descricao_treino'],
                'observacoes' => $row['observacoes_treino'],
                'data_criacao' => $row['data_criacao']
            ];
        }
        
        return $result;
    }

    public function readById($id)
    {
        $sql = "SELECT * FROM Treinos WHERE id_treino = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Treino(
                $row['id_treino'],
                $row['id_usuario'],
                $row['titulo_treino'],
                $row['descricao_treino'],
                $row['observacoes_treino'],
                $row['data_criacao']
            );
        }
        
        return null;
    }

    public function update(Treino $treino)
    {
        $sql = "UPDATE Treinos SET titulo_treino = :titulo, descricao_treino = :descricao, 
                observacoes_treino = :observacoes WHERE id_treino = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $treino->getId(),
            ':titulo' => $treino->getTitulo(),
            ':descricao' => $treino->getDescricao(),
            ':observacoes' => $treino->getObservacoes()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM Treinos WHERE id_treino = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}

