<?php
   require_once 'vendor/autoload.php';
   require_once 'google_config.php';

   session_start();

   $client = new Google_Client();
   $client->setClientId(GOOGLE_CLIENT_ID);
   $client->setClientSecret(GOOGLE_CLIENT_SECRET);
   $client->setRedirectUri(GOOGLE_REDIRECT_URI);
   if (!isset($_GET['code'])) {
    echo 'Authorization code not found';
    exit();
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$_SESSION['access_token'] = $token;

header('Location: calendar');
exit();
?>