<?php
session_start();
require_once __DIR__ . '/dummyUser.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

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

if ($email !== $dummy_user['email'] || $password !== $dummy_user['password']) {
    die("Invalid email or password.");
}

$_SESSION['otp_email'] = $email;
$_SESSION['otp_password'] = $password;
$_SESSION['otp_secret'] = $dummy_user['secret'];

// send the user to the OTP page for verification
header("Location: OTP_page.html");
exit();
?>