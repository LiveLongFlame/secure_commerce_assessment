<?php
require_once __DIR__ . '/dummyUser.php';
require_once __DIR__ . '/PHPGangsta/GoogleAuthenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $dummy_user['secret'];

$qrCodeUrl = $ga->getQRCodeGoogleUrl('Secure Commerce Assessment', $secret, 'Secure Commerce Assessment');

echo '<div>Scan the QR code below with Google Authenticator.</div>';
echo '<img src="' . $qrCodeUrl . '" alt="2FA QR code" />';
echo '<div>Secret: ' . $secret . '</div>';
 