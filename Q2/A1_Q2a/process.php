<?php
/*
    ------------------------------------------------------------
    This is the file that actually stops a bot / script from
    creating a fake account on e-commerce site:
      1) It re-validates the email/password rules on the server
         (never trusting the front-end JavaScript alone).
      2) It sends the "g-recaptcha-response" token that Google
         placed in the form back to Google's siteverify endpoint.
      3) Only if Google confirms the token is valid does the
         "account" get created.
    ------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Invalid request method.");
}

// ---------------------------------------------------------------
// 1) Collect + re-validate the submitted data on the server side
// ---------------------------------------------------------------
$email            = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password         = $_POST['password'] ?? '';
$confirmPassword  = $_POST['confirm_password'] ?? '';

// Same rule as the front end: >=10 chars, at least 1 upper, 1 lower, 1 digit
$passwordRule = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{10,}$/';

if (!$email) {
    die("Registration failed: please provide a valid email address.");
}
if (!preg_match($passwordRule, $password)) {
    die("Registration failed: password does not meet the complexity requirements (min 10 characters, upper + lower case, and a number).");
}
if ($password !== $confirmPassword) {
    die("Registration failed: the two password fields do not match.");
}

// ---------------------------------------------------------------
// 2) Verify the reCAPTCHA v2 response with Google
// ---------------------------------------------------------------
// reCAPTCHA admin console for this site (Task 1).
$recaptchaSecret = "6LdAIIotAAAAABegov_R2aWGp8WACKa0F_6pQ0xc";

$recaptchaToken = $_POST['g-recaptcha-response'] ?? '';

if (empty($recaptchaToken)) {
    // The form was submitted without ticking "I'm not a robot" at all
    // -- e.g. exactly what an automated bot / curl script would do.
    die("Registration blocked: reCAPTCHA was not completed. You might be a bot.");
}

$verifyUrl = "https://www.google.com/recaptcha/api/siteverify"
           . "?secret=" . urlencode($recaptchaSecret)
           . "&response=" . urlencode($recaptchaToken)
           . "&remoteip=" . urlencode($_SERVER['REMOTE_ADDR'] ?? '');

$verifyResponseRaw = file_get_contents($verifyUrl);
$verifyResponse    = json_decode($verifyResponseRaw, true);

if (!isset($verifyResponse['success']) || $verifyResponse['success'] !== true) {
    // Google could not confirm the challenge was solved by a human.
    die("Registration blocked: reCAPTCHA verification failed. You might be a bot.");
}

// ---------------------------------------------------------------
// 3) reCAPTCHA passed + input is valid -> "create" the account
// ---------------------------------------------------------------
$usersFile = __DIR__ . '/users_v2.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
if (!is_array($users)) {
    $users = [];
}

foreach ($users as $existingUser) {
    if (strcasecmp($existingUser['email'], $email) === 0) {
        die("Registration failed: an account with this email already exists.");
    }
}

$users[] = [
    'email'        => $email,
    // Passwords must never be stored in plain text.
    'password_hash'=> password_hash($password, PASSWORD_DEFAULT),
    'created_at'   => date('c'),
];

file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

echo "Account created successfully for " . htmlspecialchars($email) . ".";