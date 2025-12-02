<?php
/**
 * DAO AvaliacaoFisica - TechFit
 */

require_once __DIR__ . '/Conexao.php';
require_once __DIR__ . '/AvaliacaoFisica.php';

class AvaliacaoFisicaDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexao::getInstance();
        $this->createTable();
    }

    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS AvaliacoesFisicas (
            id_avaliacao INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            data_avaliacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            peso DECIMAL(5,2) NOT NULL,
            altura DECIMAL(3,2) NOT NULL,
            imc DECIMAL(4,2),
            gordura_corporal DECIMAL(4,2),
            massa_muscular DECIMAL(5,2),
            circunferencia_cintura DECIMAL(5,2),
            circunferencia_quadril DECIMAL(5,2),
            observacoes TEXT,
            id_instrutor INT,
            FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
            FOREIGN KEY (id_instrutor) REFERENCES Usuarios(id_usuario) ON DELETE SET NULL
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Erro ao criar tabela AvaliacoesFisicas: " . $e->getMessage());
        }
    }

    public function cadastrar(AvaliacaoFisica $avaliacao)
    {
        $sql = "INSERT INTO AvaliacoesFisicas (id_usuario, data_avaliacao, peso, altura, imc, 
                gordura_corporal, massa_muscular, circunferencia_cintura, circunferencia_quadril, 
                observacoes, id_instrutor) 
                VALUES (:id_usuario, :data_avaliacao, :peso, :altura, :imc, :gordura_corporal, 
                :massa_muscular, :circunferencia_cintura, :circunferencia_quadril, :observacoes, :id_instrutor)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $avaliacao->getIdUsuario(),
            ':data_avaliacao' => $avaliacao->getDataAvaliacao(),
            ':peso' => $avaliacao->getPeso(),
            ':altura' => $avaliacao->getAltura(),
            ':imc' => $avaliacao->getImc(),
            ':gordura_corporal' => $avaliacao->getGorduraCorporal(),
            ':massa_muscular' => $avaliacao->getMassaMuscular(),
            ':circunferencia_cintura' => $avaliacao->getCircunferenciaCintura(),
            ':circunferencia_quadril' => $avaliacao->getCircunferenciaQuadril(),
            ':observacoes' => $avaliacao->getObservacoes(),
            ':id_instrutor' => $avaliacao->getIdInstrutor()
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function readByUsuario($idUsuario)
    {
        $sql = "SELECT * FROM AvaliacoesFisicas WHERE id_usuario = :id_usuario ORDER BY data_avaliacao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new AvaliacaoFisica(
                $row['id_avaliacao'],
                $row['id_usuario'],
                $row['data_avaliacao'],
                $row['peso'],
                $row['altura'],
                $row['imc'],
                $row['gordura_corporal'],
                $row['massa_muscular'],
                $row['circunferencia_cintura'],
                $row['circunferencia_quadril'],
                $row['observacoes'],
                $row['id_instrutor']
            );
        }
        
        return $result;
    }

    public function readById($id)
    {
        $sql = "SELECT * FROM AvaliacoesFisicas WHERE id_avaliacao = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new AvaliacaoFisica(
                $row['id_avaliacao'],
                $row['id_usuario'],
                $row['data_avaliacao'],
                $row['peso'],
                $row['altura'],
                $row['imc'],
                $row['gordura_corporal'],
                $row['massa_muscular'],
                $row['circunferencia_cintura'],
                $row['circunferencia_quadril'],
                $row['observacoes'],
                $row['id_instrutor']
            );
        }
        
        return null;
    }

    public function readUltimaAvaliacao($idUsuario)
    {
        $sql = "SELECT * FROM AvaliacoesFisicas WHERE id_usuario = :id_usuario 
                ORDER BY data_avaliacao DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new AvaliacaoFisica(
                $row['id_avaliacao'],
                $row['id_usuario'],
                $row['data_avaliacao'],
                $row['peso'],
                $row['altura'],
                $row['imc'],
                $row['gordura_corporal'],
                $row['massa_muscular'],
                $row['circunferencia_cintura'],
                $row['circunferencia_quadril'],
                $row['observacoes'],
                $row['id_instrutor']
            );
        }
        
        return null;
    }
}

