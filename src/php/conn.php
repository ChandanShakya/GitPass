<?php
require_once 'config.php';

$dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";

try {
    $conn = new PDO($dsn, $config['user'], $config['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "An unknown error occurred: " . $e->getMessage();
}
// change to pdo
?>
