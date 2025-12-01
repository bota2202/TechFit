<?php 

    class Conexao{
        private static $instance;
        private $host="localhost";
        private $user="root";
        private $db="academia_techfit";
        private $password="@Plast..2024";

        public function getInstance(){
            if(!isset(self::$instance)){
                self::$instance=new PDO("mysql:host=$this->host;dbname=$this->db",$this->user,$this->password);
            }
        }
    }

?>