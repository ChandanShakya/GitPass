<?php 
    $host = 'mysql-server';
    $user = 'root';
    $password = 'secret';
    $database = 'id18657476_gitpass';

    $conn = mysqli_connect($host, $user, $password, $database);
    if($conn->connect_error) {
        echo "An unknown error occurred.";
    }
?>