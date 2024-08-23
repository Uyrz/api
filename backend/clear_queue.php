<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_rank'] != 1) {
    header("Location: login.php");
    exit();
}

include '../config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete all rows from the queue table and reset the ID
$sql = "DELETE FROM queue";

if ($conn->query($sql) === TRUE) {
    // Reset the auto-increment value
    $resetAutoIncrement = "ALTER TABLE queue AUTO_INCREMENT = 1";
    if ($conn->query($resetAutoIncrement) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error resetting auto-increment: " . $conn->error;
    }
} else {
    echo "Error clearing queue: " . $conn->error;
}

$conn->close();
?>
