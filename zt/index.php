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

session_start();
if (isset($_GET["aktion"]) && $_GET["aktion"] == "abmelden") {
    session_destroy(); ?>
<!doctype html>
<html>
<head>
<title>ZenTweet: Abgemeldet.</title>
<link rel="stylesheet" href="css/zentweet.dark.css" />
</head>
<body style="padding-left:10px">
<h1>Abgemeldet!</h1>
<a href="index.php">Neu anmelden.</a><br />
<br />
Leuten folgen/entfolgen? <a href='http://twitter.com'>Geht auf Twitter.com.</a><br />
<br />
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- ZenTweet.net -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-4397689707468695"
     data-ad-slot="3615427962"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
<br />
Danke, dass ihr hier wart.
</body>
</html>
<?php
    exit;
}
require_once("functions.php");
require_once("userlogin.php");
require_once("timeline.php");

// An dieser Stelle haben wir alle Benutzerdaten :-)

if (!isset($_SESSION["username"])) {
    die("fuuuuuuuu! (session leer)");
}

// ---------------------------

if (isset($_POST["aktion"]) && $_POST["aktion"] == "holetweets") {
    // hole neue Tweets und gib NUR DIESE als HTML zurück!
    $twitter_data = fetch_tweets();
    $retstring = "";

    if (isset($twitter_data["errors"])) {
        if ($twitter_data["errors"][0]["code"] == 88) {
            return "ratelimit";
        }
    }

    foreach ($twitter_data as $tweet) {
        if (tweet_is_read($tweet)) {
            continue;
        }

        // Filter: Retweets
        if (!$_SESSION["showretweets"] && preg_match("/^RT /", $tweet["text"])) {
            continue;
        }

        // Filter: Mentions
        if (/*!$_SESSION["showmentions"] && */ preg_match("/^(\.@|@)/", $tweet["text"])) { // derzeit nicht aktivierbar :P
            continue;
        }

        // Filter: Hashtags
        if (!$_SESSION["showhashtags"] && preg_match("/\s#.+/", $tweet["text"])) {
            continue;
        }

        // FIlter: Links
        if (!$_SESSION["showlinks"] && preg_match("/http:\/\/[A-Za-z0-9]+/", $tweet["text"])) {
            continue;
        }

        // FIlter: Gelaber
        if ($_SESSION["filter140chars"] && strlen($tweet["text"]) > 140) {
            continue;
        }

        $divclass = "tweet";
        if ($tweet["user"]["screen_name"] == $_SESSION["username"]) {
            $divclass .= " owntweet";
        }

        $retstring .= "<div class='$divclass'";
        
        if ($_SESSION["showavatars"] || $_SESSION["shownicknames"]) {
            $retstring .= " title=\"";

            if ($_SESSION["showavatars"]) {
                $retstring .= htmlspecialchars("<img src='" . $tweet["user"]["profile_image_url"] . "' style='width:40px;height:40px;' />");
            }
            if ($_SESSION["shownicknames"]) {
                $retstring .= htmlspecialchars("<p style='float:right;padding-left:1em'>Geschrieben von<br /><b>".$tweet["user"]["screen_name"]."</b></p>");
            }

            $retstring .= "\"";
        }

        $retstring .= ">";
        $retstring .= '<span id="fav-'.$tweet["id_str"].'" class="favstar" onclick="fav('.$tweet["id_str"].')" title="Diesen Tweet favorisieren">&#10038;</span>';
        $retstring .= make_clickable(nl2br($tweet["text"]));
        $retstring .= '<a id="link-'.$tweet["id_str"].'" class="link-orig" href="https://twitter.com/' . $tweet["user"]["screen_name"] . '/status/' . $tweet["id"] . '" target="_blank">[src]</a>';
        $retstring .= "</div><br />";
       
        markTweetAsRead($tweet["id"]);
    }

    echo $retstring;
    return;
} elseif (isset($POST["aktion"]) && $_POST["aktion"] == "userconfig") {
    // setze Benutzerflag "einstellung" auf "wert".
    setConfig($_POST["einstellung"], $_POST["wert"]);
}

// ---------------------------

include("header.php");

///////////////////

?>

<div id='timelinewrapper'>
<div id='timeline'>
<noscript>
<div class="tweet">Kein JavaScript? Keine Tweets!</div>
</noscript>
<div id="pleasewait">Bitte warten, bis neue Tweets eintreffen ...</div>
</div>
</div>

<?php

include("headerbar.php");

////////////////////

echo '<div id="newtweet" style="display:none">
<textarea id="tweetarea" name="tweetarea" onkeyup="updaterestzeichen();" placeholder="Neuer Tweet"></textarea><br />
<input type="button" class="newtweetbutton" onclick="sendtweet()" value="- mitteilen -" />
<div style="width:100%;padding-left:5px;">Zeichen übrig: <span id="restzeichen">280</span></div>
<div id="closenewtweet"><!--<a href="#" onclick="$(\'#newtweet\').hide("slow");">abbrechen</a>--></div>
<div style="clear:both"></div>
</div>';

include("footer.php");
?>
