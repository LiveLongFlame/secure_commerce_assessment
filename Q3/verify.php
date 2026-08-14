<?php
session_start();

$userCode = $_POST['code'] ?? '';

if ($userCode == $_SESSION['verificationNumber']) {
    echo "You have entered 2FA secret code correctly. Login Successful!!";
} else {
    echo "You have entered the WRONG 2FA secret code. Login Failed!!";
}
?>