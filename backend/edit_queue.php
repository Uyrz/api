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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $title = $_POST['title'];
    $time = $_POST['time'];
    $status = $_POST['status'];

    $sql = "UPDATE queue SET name=?, title=?, time=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $title, $time, $status, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
