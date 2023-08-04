<?php
include_once "conn.php";
$account_id = $_POST['account_id'];


$tag_name = trim($_POST['tag_name']);
$tag_name = strtolower($tag_name);
// Prepare the SQL statement to insert the tag
$sql = "INSERT INTO social_account_tags (account_id, tag_name) VALUES (:account_id, :tag_name)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':account_id', $account_id);
$stmt->bindParam(':tag_name', $tag_name);

// Execute the SQL statement
if ($stmt->execute()) {
    header("Location: ../index.php");
} else {

    echo "Failed to add the tag.";
}
?>
