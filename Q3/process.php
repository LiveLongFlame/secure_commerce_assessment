<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'config.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get email
    $email = trim($_POST["email"] ?? '');

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Please enter a valid email address");
    }

    // Get password
    $password = trim($_POST["password"] ?? '');

    // Validate password
    if (strlen($password) < 5) {
        die("Password must be at least 5 characters long");
    }

    // Generate a 6-digit verification code
    $verificationNumber = rand(100000, 999999);

    // Store information in session
    $_SESSION['verificationNumber'] = $verificationNumber;
    $_SESSION['email'] = $email;

    // Sending the generated number to the email address
    $mail = new PHPMailer(true);

    try {

        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $email;
        // generaeted app password from entered account
        $mail->Password   = $myPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email settings
        $mail->setFrom($email, 'Verification System');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';

        $mail->Body = "
            <h2>Your verification code is:</h2>
            <h1>$verificationNumber</h1>
        ";

        // Send email
        $mail->send();

        echo "Verification code sent to " . htmlspecialchars($email);

        // Send user to verification page
        header("Location: verify.html");
        exit();

    } catch (Exception $e) {
        echo "Email failed: " . $mail->ErrorInfo;
    }
}

?>