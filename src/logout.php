<?php
session_start();
require_once "php/conn.php";

if (isset($_SESSION['unique_id']) && isset($_GET['id']) && $_GET['id'] == $_SESSION['unique_id']) {
    $id = $_GET['id'];
    $session_id = session_id();
    $user_id = $_SESSION['unique_id'];
    $stmt = $conn->prepare("UPDATE login_track SET logout_time = CURRENT_TIMESTAMP, duration = TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, login_time)) WHERE session_id = :session_id AND user_id = :user_id");
    $stmt->bindParam(':session_id', $session_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    session_regenerate_id(true);
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
