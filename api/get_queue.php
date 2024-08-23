<?php
include '../config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit();
}

header('Content-Type: application/json');

error_log("Request method: " . $_SERVER["REQUEST_METHOD"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username'])) {
        $username = $conn->real_escape_string($_POST['username']);

        $stmt = $conn->prepare("SELECT name, title, time, id, status FROM queue WHERE username = ?");
        if ($stmt === false) {
            echo json_encode(array("error" => "Prepare failed: " . $conn->error));
            exit();
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        $queue = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $queue[] = $row;
            }
        }

        echo json_encode($queue);

        $stmt->close();
    } else {
        echo json_encode(array("error" => "Username is required"));
    }
} else {
    echo json_encode(array("error" => "Invalid request method"));
}

$conn->close();
?>
