<?php
// Alles, was noch fehlt... nützliche Hilfsfunktionen und dergleichen.

function readOrCreateConfig($username) {
    // falls die Konfiguration für diesen Nutzer noch nicht existiert, lege sie an...
    if (!file_exists("configs/$username.json")) {
        // init.-Variablen
        $vars = array();
        $vars["showavatars"] = false;
        $vars["showretweets"] = false;
        $vars["shownicknames"] = false;
        $vars["showhashtags"] = false;
        $vars["showlinks"] = false;
        $vars["readtweets"] = array();
        $vars["filter140chars"] = false;

        $fp = fopen("configs/$username.json", 'w');
        fwrite($fp, json_encode($vars));
        fclose($fp);
    }

    // dann: in Session schreiben.
    $string = file_get_contents("configs/$username.json");
    $json = json_decode($string, true);

    $_SESSION["showavatars"] = $json["showavatars"];
    $_SESSION["showretweets"] = $json["showretweets"];
    $_SESSION["shownicknames"] = $json["shownicknames"];
    $_SESSION["showhashtags"] = (isset($json["showhashtags"]) ? $json["showhashtags"] : 0);
    $_SESSION["showlinks"] = (isset($json["showlinks"]) ? $json["showlinks"] : 1);
    $_SESSION["readtweets"] = $json["readtweets"];
    $_SESSION["filter140chars"] = $json["filter140chars"];
}

function setConfig($key,$value) {
    // Ändert die Einstellung $key (z.B. showavatars) in Einstellungs-
    // datei und Sitzungsvariable.

    $username = $_SESSION["username"];

    $json_data = json_decode(file_get_contents("configs/$username.json"), true);
    $json_data[$key] = $value;

    $fp = fopen("configs/$username.json", 'w');
    fwrite($fp, json_encode($json_data));
    fclose($fp);

    $_SESSION[$key] = $value;
}

function markTweetAsRead($id) {
    $username = $_SESSION["username"];

    $json_data = json_decode(file_get_contents("configs/$username.json"), true);

    $readtweetsarray = $json_data["readtweets"];
    array_push($readtweetsarray,$id);

    // mehr als 500 gelesene Tweets zu speichern ist Quatsch.
    if (count($readtweetsarray) > 500) $readtweetsarray = array_slice($readtweetsarray,0,500);

    $json_data["readtweets"] = $readtweetsarray;

    $fp = fopen("configs/$username.json", 'w');
    fwrite($fp, json_encode($json_data));
    fclose($fp);

    $_SESSION["readtweets"] = $readtweetsarray;
}

/**
 * @link http://jonathonhill.net/2012-05-18/unshorten-urls-with-php-and-curl/
 */
function unshorten_url($url) {
    // t.co-Links auflösen
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_SSL_VERIFYPEER => FALSE, 
    ));
    curl_exec($ch);
    $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return $url;
}

function print_url($instring) {
    // Entkürzte Links können recht lang sein.
    $fixedurl = htmlspecialchars($instring);
    $ret = '<a href="' . $fixedurl .'" target="_blank" title="URL: ' . $fixedurl . '">';
    if (strlen($fixedurl) > 30) {
        $ret .= substr($fixedurl, 0, 30) . "&hellip;";
    } else {
        $ret .= $fixedurl;
    }
    $ret .= "</a>";
    return $ret;
}

function make_clickable($text) {
    // t.co-Links auflösen:
    // TODO: Das geht auch mit dem Twitter-API ("expanded_url").
    $pattern  = "~https://t.co/[A-Za-z0-9]+~";
    $text     = preg_replace_callback(
        $pattern,
        function($match) {
            return unshorten_url($match[0]);
        },
        $text
    );

    // Amazonlink geschickt unterbringen :P
    $pattern  = "~http://[^>]*?amazon.([^/]*)/([^>]*?ASIN|gp/product|exec/obidos/tg/detail/-|[^>]*?dp)/([0-9a-zA-Z]{10})[a-zA-Z0-9#/*-?&%=,._;]*~i";
    $replace  = "https://www.amazon.$1/dp/$3/?tag=hirnfi20-21";
    $text     = preg_replace($pattern,$replace,$text);

    // Hashtags automatisch verlinken
    // (Bloat. Auskommentiert.)
    // $text = preg_replace('/(#.+?)\b/','<a href="http://twitter.com/search?q=\1" target="_blank">\1</a>',$text);

    // URLs automatisch verlinken
    $urlpattern = '@(?<![.*">])\b(?:(?:https?|ftp|file)://|[a-z]\.)[-A-Z0-9+&#/%=~_|$?!:,.]*[A-Z0-9+&#/%=~_|$]@i';
    return preg_match($urlpattern, $text) ?
        preg_replace_callback($urlpattern,
            function($match) {
                return print_url($match[0]);
            }, $text) :
        $text;
}
?>
