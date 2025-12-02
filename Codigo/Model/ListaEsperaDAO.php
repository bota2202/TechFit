<?php
/**
 * DAO ListaEspera - TechFit
 */

require_once __DIR__ . '/Conexao.php';
require_once __DIR__ . '/ListaEspera.php';

class ListaEsperaDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexao::getInstance();
        $this->createTable();
    }

    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS ListaEspera (
            id_lista_espera INT AUTO_INCREMENT PRIMARY KEY,
            id_turma INT NOT NULL,
            id_usuario INT NOT NULL,
            data_inscricao DATETIME DEFAULT CURRENT_TIMESTAMP,
            prioridade INT DEFAULT 0,
            notificado BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (id_turma) REFERENCES Turmas(id_turma) ON DELETE CASCADE,
            FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
            UNIQUE KEY unique_espera (id_turma, id_usuario)
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Erro ao criar tabela ListaEspera: " . $e->getMessage());
        }
    }

    public function cadastrar(ListaEspera $lista)
    {
        $sql = "INSERT INTO ListaEspera (id_turma, id_usuario, prioridade) 
                VALUES (:id_turma, :id_usuario, :prioridade)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_turma' => $lista->getIdTurma(),
            ':id_usuario' => $lista->getIdUsuario(),
            ':prioridade' => $lista->getPrioridade()
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function readByTurma($idTurma)
    {
        $sql = "SELECT * FROM ListaEspera WHERE id_turma = :id_turma ORDER BY prioridade DESC, data_inscricao ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_turma' => $idTurma]);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new ListaEspera(
                $row['id_lista_espera'],
                $row['id_turma'],
                $row['id_usuario'],
                $row['data_inscricao'],
                $row['prioridade'],
                $row['notificado']
            );
        }
        
        return $result;
    }

    public function readByUsuario($idUsuario)
    {
        $sql = "SELECT * FROM ListaEspera WHERE id_usuario = :id_usuario ORDER BY data_inscricao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new ListaEspera(
                $row['id_lista_espera'],
                $row['id_turma'],
                $row['id_usuario'],
                $row['data_inscricao'],
                $row['prioridade'],
                $row['notificado']
            );
        }
        
        return $result;
    }

    public function delete($idTurma, $idUsuario)
    {
        $sql = "DELETE FROM ListaEspera WHERE id_turma = :id_turma AND id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_turma' => $idTurma, ':id_usuario' => $idUsuario]);
    }

    public function marcarNotificado($id)
    {
        $sql = "UPDATE ListaEspera SET notificado = TRUE WHERE id_lista_espera = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}

