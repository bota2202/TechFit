<?php 

    $server="localhost";
    $user="root";
    $db="techfit";
    $senha="@Plast..2024";

    $conn = new mysqli($server,$user,$senha,$db);

    if($conn->connect_error){
        die("Erro de conexão: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");

?>