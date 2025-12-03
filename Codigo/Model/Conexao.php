<?php

include_once __DIR__ . "/config.php";

class Conexao {
    private static $instance = null;

    public static function criarBanco() {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE " . DB_NAME);
            
            return true;
        } catch(PDOException $e) {
            error_log("Erro ao criar banco de dados: " . $e->getMessage());
            return false;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            try {
                self::criarBanco();
                
                self::$instance = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch(PDOException $e) {
                error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
                die("Erro ao conectar com o banco de dados. Tente novamente mais tarde.");
            }
        }

        return self::$instance;
    }
}
