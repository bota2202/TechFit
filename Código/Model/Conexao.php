<?php 

class Conexao {
    private static $instance = null;

    public static function getInstance() {

        $host="localhost";
        $user="root";
        $db="academia_techfit";
        $password="@Plast..2024";

        if (self::$instance === null) {
            try{
                self::$instance = new PDO(
                    "mysql:host=" . $host . ";dbname=" . $db . ";charset=utf8",
                    $user,
                    $password
                );

                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $e){
                die ("Erro na conexão com o banco de dados " .$e->getMessage());
            }
            
        }

        return self::$instance;
    }
}

?>
