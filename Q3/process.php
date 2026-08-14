<?php

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
}
   ?>