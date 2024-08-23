<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
        $response = ['success' => false, 'message' => 'All fields are required.'];
    } elseif ($newPassword !== $confirmPassword) {
        $response = ['success' => false, 'message' => 'Passwords do not match.'];
    } else {
        // Check if token is valid
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $response = ['success' => false, 'message' => 'Invalid token.'];
        } else {
            // Get the user's email
            $row = $result->fetch_assoc();
            $email = $row['email'];

            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the user's password in the users table
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $hashedPassword, $email);

            if ($updateStmt->execute()) {
                // Delete the token after successful password reset
                $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                $deleteStmt->bind_param("s", $token);
                $deleteStmt->execute();

                $response = ['success' => true, 'message' => 'Password has been reset successfully.'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to reset password.'];
            }
        }

        $stmt->close();
        $updateStmt->close();
    }

    $conn->close();
    
    // Display response message
    echo '<script>alert("' . $response['message'] . '");</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center">Reset Your Password</h3>
            <form id="resetPasswordForm" method="POST" action="">
                <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>" />

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
