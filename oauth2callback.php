<?php
/*Template Name: OAuth2 Callback*/ 
require_once __DIR__.'/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig(__DIR__.'/credentials.json');
$client->setRedirectUri( home_url(). '/oauth2callback');
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  echo "!code";
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $creds = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $_SESSION['access_token'] = $creds['access_token'];
  display_array($creds);
  $redirect_uri = home_url(). '/forms';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}