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

<div id="headerbar">
<div id="titel"><img src="../logo.png" /><span class="links">zen</span><span class="rechts">tweet</span> <span id="newtweetlink" onclick="opentweetdiv();" style="cursor:pointer" title="neuer Tweet">+</span></div>
<div id="extras-collapsed"><span title="Aktualisierung alle 12 Sekunden">&#10227;</span>
     <span class="loggedin"><span class="username">@<?php echo $_SESSION["username"]; ?></span> <a href="index.php?aktion=abmelden">(hinfort!)</a></span>
     <span onclick="showheaderbar();" style="cursor:pointer;" title="Mehr Optionen">&#9776;</span>
</div>

<div id="extras-expanded" style="display:none">
     <div id="userfield"><span class="username">@<?php echo $_SESSION["username"]; ?></span> <a href="index.php?aktion=abmelden">(x)</a></div><div onclick="hideheaderbar();" style="cursor:pointer;width:100%;text-align:right;" title="Weniger Optionen">&#9776;</div>
<div id="checkbar" title="Änderungen werden ab dem nächsten Tweet übernommen.">
  <input type="checkbox" onclick="toggleavatars()" name="tgavatars" id="tgavatars" <?php if ($_SESSION["showavatars"]) echo 'checked="checked"'; ?> />
  <label for="tgavatars"><span></span>Avatare</label>
<br />
  <input type="checkbox" onclick="togglenicknames()" name="tgnicknames" id="tgnicknames" <?php if ($_SESSION["shownicknames"]) echo 'checked="checked"'; ?> />
  <label for="tgnicknames"><span></span>Nicknamen</label>
<br />
  <input type="checkbox" onclick="togglerts()" name="tgrts" id="tgrts" <?php if ($_SESSION["showretweets"]) echo 'checked="checked"'; ?> />
  <label for="tgrts"><span></span>Retweets</label>
<br />
  <input type="checkbox" onclick="togglehashtags()" name="tghashtags" id="tghashtags" <?php if ($_SESSION["showhashtags"]) echo 'checked="checked"'; ?> />
  <label for="tghashtags"><span></span>Hashtag-Tweets</label>
<br />
  <input type="checkbox" onclick="togglelinks()" name="tglinks" id="tglinks" <?php if ($_SESSION["showlinks"]) echo 'checked="checked"'; ?> />
  <label for="tglinks"><span></span>Link-Tweets</label>
<br />
  <input type="checkbox" onclick="toggle140chars()" name="tg140chars" id="tg140chars" <?php if ($_SESSION["filter140chars"]) echo 'checked="checked"'; ?> />
  <label for="tg140chars"><span></span>Tweets &uuml;ber 140 Zeichen verstecken</label>
</div>
</div>
</div>
