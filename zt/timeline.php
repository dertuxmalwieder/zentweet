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
// Alle möglichen Timeline-Funktionen.

function tweet_is_read($tweet)
{
    // schon gelesen? dann true, sonst false!
    return (in_array($tweet["id"], $_SESSION["readtweets"]));
}

function add_quotes($str)
{
    return '"'.$str.'"';
}
function fetch_tweets()
{
    global $consumer_key, $consumer_secret;

    // Rückgabe: Array mit den Tweets (JSON)
    $token = $_SESSION["oauth_token"];
    $token_secret = $_SESSION["oauth_token_secret"];

    $host = 'api.twitter.com';
    $method = 'GET';
    $path = '/1.1/statuses/home_timeline.json';

    $query = array(
        'screen_name' => $_SESSION["username"],
        'count' => '200'
    );

    $oauth = array(
        'oauth_consumer_key' => $consumer_key,
        'oauth_token' => $token,
        'oauth_nonce' => (string)mt_rand(),
        'oauth_timestamp' => time(),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_version' => '1.0'
    );

    // Sortierung:
    $oauth = array_map("rawurlencode", $oauth);
    $query = array_map("rawurlencode", $query);

    $arr = array_merge($oauth, $query);

    asort($arr);
    ksort($arr);

    // http_build_query encodiert das gesamte Query, aber unsere
    // Anfrage enthält bereits encodierte Parameter - also einmal
    // decodieren vorm Absenden:
    $querystring = urldecode(http_build_query($arr, '', '&'));

    $url = "https://$host$path";
    $base_string = $method."&".rawurlencode($url)."&".rawurlencode($querystring);
    $key = rawurlencode($consumer_secret)."&".rawurlencode($token_secret);

    // Hash erzeugen:
    $signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));

    $url .= "?".http_build_query($query);
    $url = str_replace("&amp;", "&", $url); //Patch by @Frewuill

    $oauth['oauth_signature'] = $signature;
    ksort($oauth);

    $oauth = array_map("add_quotes", $oauth);

    $auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

    $options = array(
        CURLOPT_HTTPHEADER => array("Authorization: $auth"),
        CURLOPT_HEADER => false,
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
    );

    // Absenden und auswerten:
    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);
    curl_close($feed);

    $twitter_data = json_decode($json, 1);
    return $twitter_data;
}
?>
