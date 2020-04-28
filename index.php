<!doctype html>
<html>
<head>
<title>Codename: ZenTweet</title>
<link rel="stylesheet" href="zt/css/zentweet.dark.css" />
<link rel="icon" href="favicon.ico" type="image/x-icon" />
<meta name=viewport content="width=device-width, initial-scale=1" />
<meta name="author" content="@tux0r" />
<meta name="description" content="ZenTweet. Twittern für's Karma." />
<meta name="keywords" content="Zen, Twitter, Stream of Thoughts, Karma" />
<meta name="robots" content="index, follow" />
<meta name="DC.Creator" content="@tux0r" />
<meta name="DC.Description" content="ZenTweet. Twittern für's Karma." />
<meta name="DC.Language" content="de" />
<style type="text/css">
<!--
body {
	text-align:center;
	font-size:18px;
}

.z1 { color:white; }
.z2 { color:yellow; }

a img { border:none; }
#titel img { width:30px; height:30px; }
-->
</style>
</head>
<body>
<div id="headerbar" style="position:relative !important">
<div id="titel"><img src="logo.png" /><span class="links">zen</span><span class="rechts">tweet</span> <span id="newtweetlink">+</span></div>
</div>

<?php
function dirmtime($directory) {
    // 1. An array to hold the files.
    $last_modified_time = 0;

    // 2. Getting a handler to the specified directory
    $handler = opendir($directory);

    // 3. Looping through every content of the directory
    while ($file = readdir($handler)) {
        // 3.1 Checking if $file is not a directory
        if(is_file($directory.DIRECTORY_SEPARATOR.$file)){
            $files[] = $directory.DIRECTORY_SEPARATOR.$file;
            $filemtime = filemtime($directory.DIRECTORY_SEPARATOR.$file);
            if($filemtime>$last_modified_time) {
                $last_modified_time = $filemtime;
            }
	}
    }

    // 4. Closing the handle
    closedir($handler);

    // 5. Returning the last modified time
    return $last_modified_time;
}
?>

<br style="clear:both" />

<div class="z1">Twitter mit Inhalt.</div>

<div class="z2">Ohne Mentions.</div>

<div class="z1">Ohne Avatare.</div>

<div class="z2">Ohne Retweets.</div>

<div class="z1">Nur mit Gelassenheit.</div>

<br /><br />

<a href="zt"><img src="twitter-button.gif" alt="Hereinspaziert!" /></a>

<br /><br />

<div class="z2">Letzte &Auml;nderung: <?php setlocale(LC_TIME,"de_DE"); echo strftime("%d. %B %Y",dirmtime("./zt/.")); ?></div>

<div class="z1"><a href="https://code.rosaelefanten.org/zentweet">zentweet ist Open Source.</a></div>

<?php include("zt/footer.php"); ?>
