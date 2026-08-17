<?php
session_start();
require_once __DIR__ . '/PHPGangsta/GoogleAuthenticator.php';
require_once __DIR__ . '/dummyUser.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$code = trim($_POST["code"] ?? '');
$email = $_SESSION['otp_email'] ?? '';
$password = $_SESSION['otp_password'] ?? '';
$secret = $_SESSION['otp_secret'] ?? $dummy_user['secret'];

// we define the authenticator object
$ga = new PHPGangsta_GoogleAuthenticator();

// Now check if the email and password are correct based on the dummy user data
if ($email === $dummy_user['email'] && $password === $dummy_user['password']) {
    // Now we verify the code using the secret from the dummy user data
    $checkResult = $ga->verifyCode($secret, $code, 2); // 2 = 2*30sec clock tolerance

    if ($checkResult) {
        echo 'Verification successful. You are logged in.';
    } else {
        echo 'Verification failed. Please try again.';
    }
} else {
    echo 'Invalid email or password.';
}
?>
