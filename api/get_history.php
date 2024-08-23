<?php
include '../config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];

    $sql = "SELECT * FROM history WHERE user_id='$user_id' ORDER BY created_at DESC";
    $result = $conn->query($sql);

    $history = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
    }

    echo json_encode($history);
}

$conn->close();
?>
