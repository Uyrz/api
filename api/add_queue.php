<?php
include '../config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['name']) && isset($_POST['title']) && isset($_POST['time'])) {
        $username = $_POST['username'];
        $name = $_POST['name'];
        $title = $_POST['title'];
        $time = $_POST['time'];
        $sql = "INSERT INTO queue (username, name, title, time, status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssss", $username, $name, $title, $time);

        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error executing statement: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Required POST data not available.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
