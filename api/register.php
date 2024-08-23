<?php
header('Content-Type: application/json');
include '../config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(array("message" => "Connection failed: " . $conn->connect_error)));
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['username'], $data['password'], $data['name'], $data['phone'], $data['birthdate'], $data['address'], $data['email'])) {
    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $name = $data['name'];
    $phone = $data['phone'];
    $birthdate = $data['birthdate'];
    $address = $data['address'];
    $email = $data['email'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, name, phone, birthdate, address, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $password, $name, $phone, $birthdate, $address, $email);

    if ($stmt->execute()) {
        echo json_encode(array("message" => "Registration successful"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    $stmt->close();
} else {
    echo json_encode(array("message" => "Invalid input"));
}

$conn->close();
?>
