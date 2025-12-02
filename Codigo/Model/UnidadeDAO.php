<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ . "/Unidade.php";

class UnidadeDAO
{
    public $conn;

    public function __construct()
    {
        $this->conn = Conexao::getInstance();
        $this->criarTabela();
    }

    /**
     * Cria a tabela Unidades se não existir
     */
    private function criarTabela()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Unidades (
            id_unidade INT AUTO_INCREMENT PRIMARY KEY,
            estado_unidade VARCHAR(100) NOT NULL,
            cidade_unidade VARCHAR(100) NOT NULL,
            bairro_unidade VARCHAR(100) NOT NULL,
            rua_unidade VARCHAR(100) NOT NULL,
            numero_unidade INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
            $this->inserirDadosIniciais();
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Unidades: " . $e->getMessage());
        }
    }

    private function inserirDadosIniciais()
    {
        $sql = "SELECT COUNT(*) as total FROM Unidades";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] == 0) {
            $unidades = [
                ['SP', 'Limeira', 'Centro', 'Av. Campinas', 1500],
                ['SP', 'Campinas', 'Centro', 'R. Barão de Jaguara', 900],
                ['SP', 'Piracicaba', 'Centro', 'Av. Independência', 2100],
                ['SP', 'Sorocaba', 'Centro', 'R. XV de Novembro', 450],
                ['SP', 'Ribeirão Preto', 'Jardim Paulista', 'Av. Pres. Vargas', 1800],
                ['SP', 'Araraquara', 'Centro', 'Av. Bento de Abreu', 300],
                ['SP', 'São Carlos', 'Centro', 'R. Episcopal', 1200],
                ['SP', 'Jundiaí', 'Anhangabaú', 'Av. Jundiaí', 500],
                ['SP', 'Bauru', 'Centro', 'Av. Getúlio Vargas', 100],
                ['SP', 'São José do Rio Preto', 'Boa Vista', 'R. Bernardino', 950],
                ['SP', 'Marília', 'Fragata', 'Av. das Esmeraldas', 400],
                ['SP', 'Presidente Prudente', 'Vila Nova', 'Av. Manoel Goulart', 1300],
                ['SP', 'Americana', 'Centro', 'R. Fernando Camargo', 800],
                ['SP', 'Indaiatuba', 'Cidade Nova', 'Av. Pres. Kennedy', 700],
                ['SP', 'Barueri', 'Alphaville', 'Av. Arnaldo Rodrigues', 250]
            ];
            
            $sql = "INSERT INTO Unidades (estado_unidade, cidade_unidade, bairro_unidade, rua_unidade, numero_unidade) VALUES (?,?,?,?,?)";
            $stmt = $this->conn->prepare($sql);
            foreach ($unidades as $unidade) {
                $stmt->execute($unidade);
            }
        }
    }

    public function cadastrar(Unidade $u)
    {
        $sql = "INSERT INTO Unidades(estado_unidade, cidade_unidade, bairro_unidade, rua_unidade, numero_unidade) 
                VALUES (?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getEstado(),
            $u->getCidade(),
            $u->getBairro(),
            $u->getRua(),
            $u->getNumero()
        ]);
        return $this->conn->lastInsertId();
    }

    public function readAll()
    {
        $unidades = [];
        $sql = 'SELECT * FROM Unidades ORDER BY cidade_unidade';
        $stmt = $this->conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $unidades[] = new Unidade(
                $row['id_unidade'],
                $row['estado_unidade'],
                $row['cidade_unidade'],
                $row['bairro_unidade'],
                $row['rua_unidade'],
                $row['numero_unidade']
            );
        }
        return $unidades;
    }

    public function readById($id)
    {
        $sql = 'SELECT * FROM Unidades WHERE id_unidade=?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Unidade(
                $row['id_unidade'],
                $row['estado_unidade'],
                $row['cidade_unidade'],
                $row['bairro_unidade'],
                $row['rua_unidade'],
                $row['numero_unidade']
            );
        }
        return null;
    }

    public function update(Unidade $u)
    {
        $sql = "UPDATE Unidades SET estado_unidade=?, cidade_unidade=?, bairro_unidade=?, rua_unidade=?, numero_unidade=? 
                WHERE id_unidade=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getEstado(),
            $u->getCidade(),
            $u->getBairro(),
            $u->getRua(),
            $u->getNumero(),
            $u->getId()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM Unidades WHERE id_unidade=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }
}

