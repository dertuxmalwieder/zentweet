<?php
  // Diese Datei nur per AJAX aufrufen :-)
  // Sie sendet die neuen Tweets ab.

  // PARAMETER ($.post):
  //  1. text

  include 'config.php';
  session_start();

  if (empty($_POST["text"])) {
    // kann nicht passieren.
    exit;
  }

  $text = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $_POST["text"]); // "br2nl"

  global $consumer_key, $consumer_secret;
  $oauth = new OAuth($consumer_key,$consumer_secret,OAUTH_SIG_METHOD_HMACSHA1);

  // Read the access tokens
  $access_token = $_SESSION["oauth_token"];
  $access_token_secret = $_SESSION["oauth_token_secret"];

  $oauth->setToken($access_token, $access_token_secret);
  $args=array('status'=>substr($text,0,160));

  try
  {
    $oauth->fetch('https://api.twitter.com/1.1/statuses/update.json',$args,OAUTH_HTTP_METHOD_POST);
    $json = json_decode($oauth->getLastResponse(),true);
    if (isset($json['id'])) {
      echo "ok";
    }
    else
      echo "Fehler, bitte sag @tux0r Bescheid.";
    exit;
  }
  catch(OAuthException $E) {
    echo var_dump($E);
  }
?>
