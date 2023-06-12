<?php 
    session_start();
    require_once "php/conn.php";

    if(isset($_SESSION['unique_id']) && isset($_GET['id']) && $_GET['id'] == $_SESSION['unique_id']) {
        $id = $_GET['id'];
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
?>
