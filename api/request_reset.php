<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

require '../config.php';

$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$token = bin2hex(random_bytes(50));

$stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $token);

if ($stmt->execute()) {
    // Send reset email using PHPMailer
    $resetLink = "http://172.21.64.1/reset_password.php?token=" . $token;
    
    $mail = new PHPMailer(true);

    try {
       
        $mail->SMTPDebug = 2; 
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sirapitsila@gmail.com'; // Gmail address
        $mail->Password = 'xunnlugchenjlqyd'; // Gmail password or App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('sirapitsila@gmail.com', 'medicare');
        $mail->addAddress($email);


        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Click the following link to reset your password: <a href='$resetLink'>$resetLink</a>";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Reset link sent to your email.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error processing request.']);
}

$stmt->close();
$conn->close();
?>
