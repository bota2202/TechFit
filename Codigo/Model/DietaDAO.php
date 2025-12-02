<?php
/**
 * DAO Dieta - TechFit
 */

require_once __DIR__ . '/Conexao.php';
require_once __DIR__ . '/Dieta.php';

class DietaDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexao::getInstance();
        $this->createTable();
    }

    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Dietas (
            id_dieta INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            titulo_dieta VARCHAR(255) NOT NULL,
            descricao_dieta TEXT NOT NULL,
            observacoes_dieta TEXT,
            data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Erro ao criar tabela Dietas: " . $e->getMessage());
        }
    }

    public function cadastrar(Dieta $dieta)
    {
        $sql = "INSERT INTO Dietas (id_usuario, titulo_dieta, descricao_dieta, observacoes_dieta) 
                VALUES (:id_usuario, :titulo, :descricao, :observacoes)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $dieta->getIdUsuario(),
            ':titulo' => $dieta->getTitulo(),
            ':descricao' => $dieta->getDescricao(),
            ':observacoes' => $dieta->getObservacoes()
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function readByUsuarioId($idUsuario)
    {
        $sql = "SELECT * FROM Dietas WHERE id_usuario = :id_usuario ORDER BY data_criacao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = [
                'id' => $row['id_dieta'],
                'titulo' => $row['titulo_dieta'],
                'descricao' => $row['descricao_dieta'],
                'observacoes' => $row['observacoes_dieta'],
                'data_criacao' => $row['data_criacao']
            ];
        }
        
        return $result;
    }

    public function readById($id)
    {
        $sql = "SELECT * FROM Dietas WHERE id_dieta = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Dieta(
                $row['id_dieta'],
                $row['id_usuario'],
                $row['titulo_dieta'],
                $row['descricao_dieta'],
                $row['observacoes_dieta'],
                $row['data_criacao']
            );
        }
        
        return null;
    }

    public function update(Dieta $dieta)
    {
        $sql = "UPDATE Dietas SET titulo_dieta = :titulo, descricao_dieta = :descricao, 
                observacoes_dieta = :observacoes WHERE id_dieta = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $dieta->getId(),
            ':titulo' => $dieta->getTitulo(),
            ':descricao' => $dieta->getDescricao(),
            ':observacoes' => $dieta->getObservacoes()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM Dietas WHERE id_dieta = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}

