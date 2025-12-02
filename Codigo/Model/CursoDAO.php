<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ . "/Curso.php";

class CursoDAO
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
        $sql = "CREATE TABLE IF NOT EXISTS Cursos (
            id_curso INT AUTO_INCREMENT PRIMARY KEY,
            nome_curso VARCHAR(255) NOT NULL,
            tipo_curso VARCHAR(100) NOT NULL,
            descricao_curso VARCHAR(500),
            preco_curso DECIMAL(5,2) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Cursos: " . $e->getMessage());
        }
    }

    private function inserirDadosIniciais()
    {
        $sql = "SELECT COUNT(*) as total FROM Cursos";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] == 0) {
            $cursos = [
                ['Musculação', 'forca', 'Treinos completos para ganho de massa e força com acompanhamento profissional especializado.', 99.00],
                ['Yoga', 'mente-corpo', 'Equilíbrio, alongamento e bem-estar físico e mental com professores especializados.', 149.00],
                ['Pilates', 'mente-corpo', 'Fortaleça seu corpo e melhore sua postura com aulas modernas de Pilates.', 149.00],
                ['CrossFit', 'forca', 'Treinos intensos de alta performance para resistência e condicionamento físico.', 199.00],
                ['Spinning', 'cardio', 'Aulas dinâmicas de bike indoor com muita energia e queima calórica intensa.', 149.00],
                ['Zumba', 'cardio', 'Dance, divirta-se e entre em forma com coreografias animadas e intensas.', 99.00],
                ['Muay Thai', 'lutas', 'Defesa pessoal e condicionamento físico com artes marciais de alto impacto.', 199.00],
                ['Natação', 'cardio', 'Aulas para todas as idades, desenvolvendo resistência e saúde cardiovascular.', 149.00],
                ['Treinamento Funcional', 'forca', 'Movimentos naturais para melhorar força, coordenação e qualidade de vida.', 149.00]
            ];
            
            $sql = "INSERT INTO Cursos (nome_curso, tipo_curso, descricao_curso, preco_curso) VALUES (?,?,?,?)";
            $stmt = $this->conn->prepare($sql);
            foreach ($cursos as $curso) {
                $stmt->execute($curso);
            }
        }
    }

    public function cadastrar(Curso $c)
    {
        $sql = "INSERT INTO Cursos(nome_curso, tipo_curso, descricao_curso, preco_curso) VALUES (?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $c->getNome(),
            $c->getTipo(),
            $c->getDescricao(),
            $c->getPreco()
        ]);
        return $this->conn->lastInsertId();
    }

    public function readAll()
    {
        $cursos = [];
        $sql = 'SELECT * FROM Cursos ORDER BY nome_curso';
        $stmt = $this->conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cursos[] = new Curso(
                $row['id_curso'],
                $row['nome_curso'],
                $row['tipo_curso'],
                $row['descricao_curso'],
                $row['preco_curso']
            );
        }
        return $cursos;
    }

    public function readById($id)
    {
        $sql = 'SELECT * FROM Cursos WHERE id_curso=?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Curso(
                $row['id_curso'],
                $row['nome_curso'],
                $row['tipo_curso'],
                $row['descricao_curso'],
                $row['preco_curso']
            );
        }
        return null;
    }

    public function readByTipo($tipo)
    {
        $cursos = [];
        $sql = 'SELECT * FROM Cursos WHERE tipo_curso=? ORDER BY nome_curso';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tipo]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cursos[] = new Curso(
                $row['id_curso'],
                $row['nome_curso'],
                $row['tipo_curso'],
                $row['descricao_curso'],
                $row['preco_curso']
            );
        }
        return $cursos;
    }

    public function update(Curso $c)
    {
        $sql = "UPDATE Cursos SET nome_curso=?, tipo_curso=?, descricao_curso=?, preco_curso=? WHERE id_curso=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $c->getNome(),
            $c->getTipo(),
            $c->getDescricao(),
            $c->getPreco(),
            $c->getId()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM Cursos WHERE id_curso=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }
}

