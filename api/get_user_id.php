<?php
include '../config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get user ID by username
function getUserIdByUsername($username, $conn) {
    // Sanitize the input to prevent SQL injection
    $username = $conn->real_escape_string($username);

    // Prepare the SQL statement to get user ID
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if a user was found and fetch the user_id
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id'];
    } else {
        // If no user is found, return null
        return null;
    }

    // Close the statement
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username is set and sanitize input
    if (isset($_POST['username'])) {
        $username = $_POST['username'];

        // Get user ID from username
        $user_id = getUserIdByUsername($username, $conn);

        if ($user_id !== null) {
            // Prepare the SQL statement to fetch queue for the user
            $stmt = $conn->prepare("SELECT name, title, time, id, status FROM queue WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);

            // Execute the statement
            $stmt->execute();

            // Get the result
            $result = $stmt->get_result();

            $queue = array();

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $queue[] = $row;
                }
            }

            // Encode the queue array to JSON format
            echo json_encode($queue);

            // Close the statement
            $stmt->close();
        } else {
            echo json_encode(array("error" => "User not found"));
        }
    } else {
        echo json_encode(array("error" => "Username is required"));
    }
} else {
    echo json_encode(array("error" => "Invalid request method"));
}

// Close connection
$conn->close();
?>
