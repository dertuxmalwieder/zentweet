<?php
  // Diese Datei nur per AJAX aufrufen :-)
  // Sie favorisiert den Tweet <ID>.

  // PARAMETER ($.post):
  //  1. id

  include 'config.php';
  session_start();

  if (empty($_POST["id"])) {
    // kann nicht passieren.
    exit;
  }

  global $consumer_key, $consumer_secret;
  $oauth = new OAuth($consumer_key,$consumer_secret,OAUTH_SIG_METHOD_HMACSHA1);

  // Read the access tokens
  $access_token = $_SESSION["oauth_token"];
  $access_token_secret = $_SESSION["oauth_token_secret"];

  $oauth->setToken($access_token, $access_token_secret);
  $args=array('id'=>intval($_POST["id"]));

  try
  {
    $oauth->fetch('https://api.twitter.com/1.1/favorites/create.json',$args,OAUTH_HTTP_METHOD_POST);
    $json = json_decode($oauth->getLastResponse(),true);
    if (isset($json['id'])) {
      echo "ok";
    }
    else
      echo "Fehler, bitte sag' @tux0r Bescheid.";
    exit;
  }
  catch(OAuthException $E) {
    var_dump($E);
  }
?>
