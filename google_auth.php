<?php
   require_once 'vendor/autoload.php';
   require_once 'google_config.php';

   $client = new Google_Client();
   $client->setClientId(GOOGLE_CLIENT_ID);
   $client->setClientSecret(GOOGLE_CLIENT_SECRET);
   $client->setRedirectUri(GOOGLE_REDIRECT_URI);
   $client->addScope(Google_Service_Calendar::CALENDAR);

   $authUrl = $client->createAuthUrl();
   header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
   exit();
   ?>