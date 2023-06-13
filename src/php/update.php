<?php
session_start();
include_once "../php/conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['upid'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT username, password FROM social_accounts WHERE account_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $prevUsername = $prevPassword = "";
    foreach ($array as $arr) {
        $prevUsername = $arr["username"];
        $prevPassword = $arr["password"];
    }

    if (!($username === $prevUsername && $password === $prevPassword)) {

        $sql = "INSERT INTO password_history(account_id,previous_username,previous_password) VALUES(:id,:prevUsername,:prevPassword);";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':prevUsername', $prevUsername);
        $stmt->bindParam(':prevPassword', $prevPassword);
        $stmt->execute();

        $sql = "UPDATE social_accounts SET username = :username, password = AES_ENCRYPT(:password, :encryptionKey) WHERE account_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':encryptionKey', $_SESSION['password']);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
header("Location: ../index.php");
