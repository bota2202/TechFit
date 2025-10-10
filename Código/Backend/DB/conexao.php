<?php 

    $server="localhost";
    $user="root";
    $db="techfit";
    $senha="@Plast..2024";

    $conn = new mysqli($server,$user,$senha,$db);

    if($conn->connect_error){
        echo "Erro";
    }else{
        echo "Sucesso";
    }

?>