<?php
/*
 * The contents of this file are subject to the terms of the
 * Common Development and Distribution License, Version 1.0 only
 * (the "License").  You may not use this file except in compliance
 * with the License.
 *
 * See the file LICENSE in this distribution for details.
 * A copy of the CDDL is also available via the Internet at
 * http://www.opensource.org/licenses/cddl1.txt
 *
 * When distributing Covered Code, include this CDDL HEADER in each
 * file and include the contents of the LICENSE file from this
 * distribution.
 */
?>

<?php
// Anmeldung für Benutzer

if (!isset($_SESSION)) {
    session_start();
}

require_once('config.php');
require_once('functions.php');

define('ONLINE', ini_get('session.gc_maxlifetime'));

$requestUrl   = 'https://api.twitter.com/oauth/request_token';
$authorizeUrl = 'https://api.twitter.com/oauth/authenticate';

$indexurl = "http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/index.php";

if (!empty($_SESSION['online'])) {
    // automatische Ab- und Neuanmeldung nach Ende der Sitzungszeit, quasi.
    if (time() - $_SESSION['online'] > ONLINE) {
        $_SESSION = array();
        session_write_close();

        // Sitzung abgelaufen. Ja nun... neu laden evtl.
        header("Location: userlogin.php");
        exit;
    }
}

if (isset($_GET["denied"])) {
    // Benutzer hat die Anmeldung abgebrochen. Schade.
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION["oauth_request_token"])) {
    // Der Benutzer hat noch keine Anmeldung.
    $oauth = new OAuth($consumer_key, $consumer_secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);

    $tokenInfo                       = $oauth->getRequestToken($requestUrl);
    $_SESSION['oauth_request_token'] = $tokenInfo['oauth_token_secret'];
    $_SESSION['online']              = time();

    $location = $authorizeUrl . '?oauth_token=' . $tokenInfo['oauth_token'];

    session_write_close();

    header('Location: ' . $location);
    exit;
} elseif (!isset($_SESSION["oauth_token"])) {
    if (!isset($_GET['oauth_token'])) {
        // Der Benutzer ist noch angemeldet, aber seine Session ist irgendwie verschwunden.
        // Vermutlich Browser neu gestartet oder so etwas.
        // Daher bekommt er auch kein neues oauth_token. Also fangen wir von vorn an.
        $_SESSION = array();
        session_write_close();

        include_once("header.php");

        echo "<style type='text/css'>";
        echo "body { color: white; font-family: sans-serif; }\n";
        echo "a:link, a:visited { color:yellow; }\n";
        echo "</style>";
        echo "<br /><br />";
        echo "Twitter hat deine Anmeldung vergessen.<br /><br />\n";
        echo "<b>Hast du eventuell deinen Browser neu gestartet oder den Tab neu geöffnet, ohne dich vorher abzumelden?</b><br /><br />\n";
        echo "Klicke bitte <a href='index.php?aktion=abmelden'>hier</a> und melde dich erneut bei Twitter an.<br />Und entschuldige die Unannehmlichkeiten!<br /><br />";

        include_once("footer.php");

        // Sitzung abgelaufen. Ja nun... neu laden evtl.
        //header("Location: /userlogin.php");
        exit;
    }
    $oauth = new OAuth($consumer_key, $consumer_secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);

    $accessUrl = 'https://api.twitter.com/oauth/access_token';

    $oauth->setToken($_GET['oauth_token'], $_SESSION['oauth_request_token']);
    $tokenInfo = $oauth->getAccessToken($accessUrl);

    $_SESSION["oauth_token"]        = $tokenInfo['oauth_token'];
    $_SESSION['oauth_token_secret'] = $tokenInfo['oauth_token_secret'];
    $_SESSION['online']             = time();

    $oauth->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

    $oauth->fetch('https://api.twitter.com/1.1/account/verify_credentials.json');
    $json = json_decode($oauth->getLastResponse());
    $_SESSION["username"] = (string)$json->screen_name; // got it. ;-)

    session_write_close();

    header("Location: $indexurl");
    exit;
} elseif (isset($_SESSION["username"])) {
    // Sollte doch alles in Ordnung sein. :-)
    // Alle anderen Fälle werden mir hoffentlich gemeldet. ;-)
    $_SESSION['online'] = time();

    //session_write_close();

    //header("Location: $indexurl");
    //exit;

    // Benutzerkonfiguration:
    readOrCreateConfig($_SESSION["username"]);
} else {
    /*
        echo "<!--<pre>SESSION:";
        echo var_dump($_SESSION);
        echo "</pre> -->";
    */
}
?>
