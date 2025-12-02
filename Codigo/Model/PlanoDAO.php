<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ . "/Plano.php";

class PlanoDAO
{
    public $conn;

    public function __construct()
    {
        $this->conn = Conexao::getInstance();
        $this->criarTabela();
        $this->inserirDadosIniciais();
    }

    private function criarTabela()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Planos (
            id_plano INT AUTO_INCREMENT PRIMARY KEY,
            preco_plano DECIMAL(5,2) NOT NULL,
            descricao_plano VARCHAR(500)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Planos: " . $e->getMessage());
        }
    }

    private function inserirDadosIniciais()
    {
        $sql = "SELECT COUNT(*) as total FROM Planos";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] == 0) {
            $planos = [
                [99.00, '1 unidade, Musculação, App TechFit, sem aulas coletivas'],
                [149.00, 'Todas unidades, Até 3 cursos, Aulas coletivas, App TechFit'],
                [199.00, 'Todas unidades, Todos os cursos a vontade, Aulas coletivas, App TechFit pro']
            ];
            
            $sql = "INSERT INTO Planos (preco_plano, descricao_plano) VALUES (?,?)";
            $stmt = $this->conn->prepare($sql);
            foreach ($planos as $plano) {
                $stmt->execute($plano);
            }
        }
    }

    public function cadastrar(Plano $p)
    {
        $sql = "INSERT INTO Planos(preco_plano, descricao_plano) VALUES (?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$p->getPreco(), $p->getDescricao()]);
        return $this->conn->lastInsertId();
    }

    public function readAll()
    {
        $planos = [];
        $sql = 'SELECT * FROM Planos ORDER BY preco_plano';
        $stmt = $this->conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $planos[] = new Plano(
                $row['id_plano'],
                $row['preco_plano'],
                $row['descricao_plano']
            );
        }
        return $planos;
    }

    public function readById($id)
    {
        $sql = 'SELECT * FROM Planos WHERE id_plano=?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Plano($row['id_plano'], $row['preco_plano'], $row['descricao_plano']);
        }
        return null;
    }

    public function update(Plano $p)
    {
        $sql = "UPDATE Planos SET preco_plano=?, descricao_plano=? WHERE id_plano=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$p->getPreco(), $p->getDescricao(), $p->getId()]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM Planos WHERE id_plano=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }
}

