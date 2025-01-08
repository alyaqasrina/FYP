<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/oauth2callback');

if (!isset($_GET['code'])) {
    header('Location: ' . filter_var('calendar_integration.php', FILTER_SANITIZE_URL));
    exit();
} else {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: ' . filter_var('calendar_integration.php', FILTER_SANITIZE_URL));
    exit();
}
?>
