<?php 
    include_once "../php/conn.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST['upid'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $sql = mysqli_query($conn, "UPDATE `passwords` SET `username` = '$username',`password` = '$password' WHERE `id` = '$id';");
    }
    header("Location:http://" . $_SERVER['HTTP_HOST']."/index.php");

?>