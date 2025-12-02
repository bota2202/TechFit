<?php
/**
 * DAO Acesso - TechFit
 */

require_once __DIR__ . '/Conexao.php';
require_once __DIR__ . '/Acesso.php';

class AcessoDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexao::getInstance();
        $this->createTable();
    }

    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Acessos (
            id_acesso INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            id_unidade INT NOT NULL,
            data_acesso DATETIME DEFAULT CURRENT_TIMESTAMP,
            tipo_acesso VARCHAR(20) DEFAULT 'entrada',
            metodo_acesso VARCHAR(20) DEFAULT 'qr_code',
            qr_code VARCHAR(255),
            FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
            FOREIGN KEY (id_unidade) REFERENCES Unidades(id_unidade) ON DELETE CASCADE
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Erro ao criar tabela Acessos: " . $e->getMessage());
        }
    }

    public function registrarAcesso(Acesso $acesso)
    {
        $sql = "INSERT INTO Acessos (id_usuario, id_unidade, tipo_acesso, metodo_acesso, qr_code) 
                VALUES (:id_usuario, :id_unidade, :tipo_acesso, :metodo_acesso, :qr_code)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $acesso->getIdUsuario(),
            ':id_unidade' => $acesso->getIdUnidade(),
            ':tipo_acesso' => $acesso->getTipoAcesso(),
            ':metodo_acesso' => $acesso->getMetodoAcesso(),
            ':qr_code' => $acesso->getQrCode()
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function readByUsuario($idUsuario, $dataInicio = null, $dataFim = null)
    {
        $sql = "SELECT * FROM Acessos WHERE id_usuario = :id_usuario";
        
        $params = [':id_usuario' => $idUsuario];
        
        if ($dataInicio) {
            $sql .= " AND DATE(data_acesso) >= :data_inicio";
            $params[':data_inicio'] = $dataInicio;
        }
        
        if ($dataFim) {
            $sql .= " AND DATE(data_acesso) <= :data_fim";
            $params[':data_fim'] = $dataFim;
        }
        
        $sql .= " ORDER BY data_acesso DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Acesso(
                $row['id_acesso'],
                $row['id_usuario'],
                $row['id_unidade'],
                $row['data_acesso'],
                $row['tipo_acesso'],
                $row['metodo_acesso'],
                $row['qr_code']
            );
        }
        
        return $result;
    }

    public function readByUnidade($idUnidade, $dataInicio = null, $dataFim = null)
    {
        $sql = "SELECT * FROM Acessos WHERE id_unidade = :id_unidade";
        
        $params = [':id_unidade' => $idUnidade];
        
        if ($dataInicio) {
            $sql .= " AND DATE(data_acesso) >= :data_inicio";
            $params[':data_inicio'] = $dataInicio;
        }
        
        if ($dataFim) {
            $sql .= " AND DATE(data_acesso) <= :data_fim";
            $params[':data_fim'] = $dataFim;
        }
        
        $sql .= " ORDER BY data_acesso DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Acesso(
                $row['id_acesso'],
                $row['id_usuario'],
                $row['id_unidade'],
                $row['data_acesso'],
                $row['tipo_acesso'],
                $row['metodo_acesso'],
                $row['qr_code']
            );
        }
        
        return $result;
    }

    public function gerarRelatorioUtilizacao($idUnidade = null, $dataInicio = null, $dataFim = null)
    {
        $sql = "SELECT 
                    DATE(data_acesso) as data,
                    COUNT(DISTINCT id_usuario) as usuarios_unicos,
                    COUNT(*) as total_acessos,
                    SUM(CASE WHEN tipo_acesso = 'entrada' THEN 1 ELSE 0 END) as entradas,
                    SUM(CASE WHEN tipo_acesso = 'saida' THEN 1 ELSE 0 END) as saidas
                FROM Acessos";
        
        $params = [];
        $conditions = [];
        
        if ($idUnidade) {
            $conditions[] = "id_unidade = :id_unidade";
            $params[':id_unidade'] = $idUnidade;
        }
        
        if ($dataInicio) {
            $conditions[] = "DATE(data_acesso) >= :data_inicio";
            $params[':data_inicio'] = $dataInicio;
        }
        
        if ($dataFim) {
            $conditions[] = "DATE(data_acesso) <= :data_fim";
            $params[':data_fim'] = $dataFim;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " GROUP BY DATE(data_acesso) ORDER BY data DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

