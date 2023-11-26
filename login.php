<?php
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed

include "fbconfig.php";

session_start();
$fb = new Facebook\Facebook([
  'app_id' => $fb_app_id, // Replace {app-id} with your app id
  'app_secret' => $fb_app_secret,
  'default_graph_version' => 'v3.2',
  ]);

$helper = $fb->getRedirectLoginHelper();

//  this redirect thing needs HTTPS
$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('https://example.com/fb-callback.php', $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';


?>
