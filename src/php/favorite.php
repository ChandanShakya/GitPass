<?php 
    include_once "../php/conn.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST['favid'];
        $stmt = $conn->prepare("UPDATE social_account_metadata SET favorite = NOT favorite WHERE metadata_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    header("Location: ../index.php");
?>
