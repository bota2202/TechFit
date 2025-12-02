<?php

include_once __DIR__ . "/Conexao.php";
include_once __DIR__ . "/Pagamento.php";

class PagamentoDAO
{
    public $conn;

    public function __construct()
    {
        $this->conn = Conexao::getInstance();
        $this->criarTabela();
    }

    private function criarTabela()
    {
        // Garante que as tabelas dependentes existam
        $sql = "CREATE TABLE IF NOT EXISTS Planos (
            id_plano INT AUTO_INCREMENT PRIMARY KEY,
            preco_plano DECIMAL(5,2) NOT NULL,
            descricao_plano VARCHAR(500)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS Usuarios (
            id_usuario INT AUTO_INCREMENT PRIMARY KEY,
            email_usuario VARCHAR(255) NOT NULL UNIQUE,
            senha_usuario_hash VARCHAR(255) NOT NULL,
            nome_usuario VARCHAR(255) NOT NULL,
            telefone_usuario VARCHAR(15) NOT NULL,
            cpf_usuario VARCHAR(14) UNIQUE NOT NULL,
            tipo_usuario TINYINT NOT NULL DEFAULT 3,
            cidade_usuario VARCHAR(255),
            estado_usuario VARCHAR(255),
            bairro_usuario VARCHAR(255),
            rua_usuario VARCHAR(255)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS Pagamentos (
            id_pagamento INT NOT NULL AUTO_INCREMENT,
            id_usuario INT NOT NULL,
            id_plano INT NOT NULL,
            tipo_pagamento TINYINT,
            valor_pagamento DECIMAL(5,2) NOT NULL,
            data_pagamento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_pagamento),
            FOREIGN KEY (id_usuario) REFERENCES Usuarios (id_usuario) ON DELETE CASCADE,
            FOREIGN KEY (id_plano) REFERENCES Planos (id_plano) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch(PDOException $e) {
            error_log("Erro ao criar tabela Pagamentos: " . $e->getMessage());
        }
    }

    public function cadastrar(Pagamento $p)
    {
        $sql = "INSERT INTO Pagamentos(id_usuario, id_plano, tipo_pagamento, valor_pagamento, data_pagamento) 
                VALUES (?,?,?,?,NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $p->getIdUsuario(),
            $p->getIdPlano(),
            $p->getTipo(),
            $p->getValor()
        ]);
        return $this->conn->lastInsertId();
    }

    public function readAll()
    {
        $pagamentos = [];
        $sql = 'SELECT * FROM Pagamentos ORDER BY data_pagamento DESC';
        $stmt = $this->conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pagamentos[] = new Pagamento(
                $row['id_pagamento'],
                $row['id_usuario'],
                $row['id_plano'],
                $row['tipo_pagamento'],
                $row['valor_pagamento'],
                $row['data_pagamento']
            );
        }
        return $pagamentos;
    }

    public function readById($id)
    {
        $sql = 'SELECT * FROM Pagamentos WHERE id_pagamento=?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Pagamento(
                $row['id_pagamento'],
                $row['id_usuario'],
                $row['id_plano'],
                $row['tipo_pagamento'],
                $row['valor_pagamento'],
                $row['data_pagamento']
            );
        }
        return null;
    }

    public function readByUsuario($idUsuario)
    {
        $pagamentos = [];
        $sql = 'SELECT * FROM Pagamentos WHERE id_usuario=? ORDER BY data_pagamento DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idUsuario]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pagamentos[] = new Pagamento(
                $row['id_pagamento'],
                $row['id_usuario'],
                $row['id_plano'],
                $row['tipo_pagamento'],
                $row['valor_pagamento'],
                $row['data_pagamento']
            );
        }
        return $pagamentos;
    }
}

