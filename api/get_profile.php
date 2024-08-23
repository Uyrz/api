<?php
include '../config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

if (!isset($_GET['username']) || empty($_GET['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Username parameter is missing']);
    exit;
}

$user = $_GET['username'];

$stmt = $conn->prepare("SELECT name, phone, birthdate, address, email FROM users WHERE username = ?");
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to prepare SQL statement']);
    exit;
}

$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode($row);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No user found']);
}

$stmt->close();
$conn->close();
?>
