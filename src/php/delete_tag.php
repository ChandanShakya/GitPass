<?php
include_once "conn.php";
$account_id = $_POST['account_id'];
$tag_name = trim($_POST['tag_name']);

// Prepare the SQL statement to delete the tag
$sql = "DELETE FROM social_account_tags WHERE account_id = :account_id AND tag_name = :tag_name";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':account_id', $account_id);
$stmt->bindParam(':tag_name', $tag_name);

// Execute the SQL statement
if ($stmt->execute()) {
    header("Location: ../index.php");
} else {
    echo "Failed to delete the tag.";
}
?>
