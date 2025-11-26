<?php
class Conexao
{
    private static $instance = null;
    private $conn;
    private $host = 'localhost';
    private $db = 'projeto_techfit';
    private $user = 'root';
    private $senha = '@Plast..2024';

    private function __construct()
    {
        try {
            $pdo = new PDO("mysql:host={$this->host}", $this->user, $this->senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS {$this->db} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE {$this->db}");

            $this->conn = $pdo;
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Conexao();
        }
        return self::$instance->conn;
    }
}
