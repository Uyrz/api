<?php
header("Content-Type: application/json");
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

$username = $data->username;
$password = $data->password;

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    echo json_encode(["message" => "Login successful", "rank" => $user['rank']]);
} else {
    echo json_encode(["message" => "Invalid username or password"]);
}

$stmt->close();
$conn->close();
?>
