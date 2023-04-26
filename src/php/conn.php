<?php
$host = 'mysql-server';
$user = 'root';
$password = 'secret';
$database = 'id18657476_gitpass';

$dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

try {
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "An unknown error occurred: " . $e->getMessage();
}
?>
