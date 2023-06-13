<?php 
include_once "../php/conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['delid'];
    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare("DELETE FROM social_accounts WHERE account_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

header("Location: ../index.php");