<?php
/*
    ============================================================
    Unlike v2 (a simple pass/fail), Google returns a "score"
    between 0.0 and 1.0 describing how confident it is the
    request came from a genuine human, plus the "action" name
    that asked it to score. The site owner decides the cut-off
    (threshold) for what counts as acceptable.
    ============================================================
*/
 
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Invalid request method.");
}
 
// ---------------------------------------------------------------
// 1) Collect + re-validate the submitted data on the server side
// ---------------------------------------------------------------
$username        = trim($_POST['username'] ?? '');
$password         = $_POST['password'] ?? '';
$confirmPassword  = $_POST['confirm_password'] ?? '';
 
$passwordRule = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{10,}$/';
 
if ($username === '') {
    die("Registration failed: please provide a username.");
}
if (!preg_match($passwordRule, $password)) {
    die("Registration failed: password does not meet the complexity requirements (min 10 characters, upper + lower case, and a number).");
}
if ($password !== $confirmPassword) {
    die("Registration failed: the two password fields do not match.");
}
 
// ---------------------------------------------------------------
// 2) Verify the reCAPTCHA v3 token + score with Google
// ---------------------------------------------------------------
$recaptchaSecret = "6LfhN4otAAAAANDsEmDzK9zireqdBZotij7HeEcU";
 
// Google's own docs suggest 0.5 as a
// sensible default starting point (0.0 = bot, 1.0 = human).
$scoreThreshold = 0.5;
$expectedAction = 'create_account';
 
$recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
 
if (empty($recaptchaToken)) {
    die("Registration blocked: reCAPTCHA token missing. You might be a bot.");
}
 
$verifyUrl = "https://www.google.com/recaptcha/api/siteverify"
           . "?secret=" . urlencode($recaptchaSecret)
           . "&response=" . urlencode($recaptchaToken)
           . "&remoteip=" . urlencode($_SERVER['REMOTE_ADDR'] ?? '');
 
$verifyResponseRaw = file_get_contents($verifyUrl);
$verifyResponse    = json_decode($verifyResponseRaw, true);
 
$success = $verifyResponse['success']       ?? false;
$score   = $verifyResponse['score']         ?? null;
$action  = $verifyResponse['action']        ?? null;
 
if (!$success) {
    die("Registration blocked: reCAPTCHA could not validate this request.");
}
if ($action !== $expectedAction) {
    // The token was for a different action than the one this form expects -
    // a sign the token may have been reused/replayed by an attacker.
    die("Registration blocked: reCAPTCHA action mismatch.");
}
if ($score === null || $score < $scoreThreshold) {
    die("Registration blocked: this request looked automated (reCAPTCHA score: "
        . htmlspecialchars($score ?? 'N/A') . "). Please try again from a normal browser.");
}
 
// ---------------------------------------------------------------
// 3) Score passed threshold + input is valid -> "create" the account
// ---------------------------------------------------------------
$usersFile = __DIR__ . '/users_v3.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
if (!is_array($users)) {
    $users = [];
}
 
foreach ($users as $existingUser) {
    if (strcasecmp($existingUser['username'], $username) === 0) {
        die("Registration failed: this username is already taken.");
    }
}
 
$users[] = [
    'username'      => $username,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'recaptcha_score' => $score,
    'created_at'    => date('c'),
];
 
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
 
echo "Account created successfully for " . htmlspecialchars($username)
   . ". (reCAPTCHA v3 score: " . htmlspecialchars($score) . ")";
