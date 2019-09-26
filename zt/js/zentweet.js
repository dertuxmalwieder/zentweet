function toggleavatars() {
    setConfig("showavatars",(document.getElementById("tgavatars").checked == true) ? 1 : 0);
}

function togglenicknames() {
    setConfig("shownicknames",(document.getElementById("tgnicknames").checked == true) ? 1 : 0);
}

function togglerts() {
    setConfig("showretweets",(document.getElementById("tgrts").checked == true) ? 1 : 0);
}

function togglehashtags() {
    setConfig("showhashtags",(document.getElementById("tghashtags").checked == true) ? 1 : 0);
}

function togglelinks() {
    setConfig("showlinks",(document.getElementById("tglinks").checked == true) ? 1 : 0);
}

function toggle140chars() {
    setConfig("filter140chars",(document.getElementById("tg140chars").checked == true) ? 1 : 0);
}


function setConfig(key,value) {
    $.post("index.php", {
        aktion: "userconfig",
        einstellung: key,
        wert: value
    });  
}

///////////////////////////////

var breakthecron = false;

function timelineCron() {
    // Timeline automatisch aktualisieren
    // Twitter-API 1.1: max. 180 Anfragen pro 15 Minuten.
    // --> max. 1 Anfrage alle 5 Sekunden.

    if (breakthecron) return;

    // Lade neue Tweets in das #timeline-DIV:
    fetchNewTweets();

    // Rekursion:
    setTimeout("timelineCron()",12000); // 12 Sekunden
}

function fetchNewTweets() {
    $.post("index.php", {
        aktion: "holetweets"
    }).done(function(data) {
        if (data == "ratelimit") {
            // wir rufen zu schnell Tweets ab. Ups. :-)
            breakthecron = true;
            return;
        }
        if (data.length > 1) {
            //alert(data);
            $(data).hide().prependTo("#timeline").fadeIn("slow");
            $("#pleasewait").hide();

            // Tooltips:
            $('.tweet').each(function() {
                $(this).tooltipster({
			contentAsHTML: true,
			plugins: ['follower']
		});
            });
        }
        // else: keine neuen Tweets. Tu' nix.
    });
}

///////////////////////////////

var headervisible = false;

function showheaderbar() {
    $("#extras-expanded").slideDown();
    $("#extras-collapsed").hide("fast");
//    $("#footer").show("fast");
    headervisible = true;
    sizeContent();
}

function hideheaderbar() {
    $("#extras-expanded").slideUp();
    $("#extras-collapsed").show("fast");
//    $("#footer").hide("fast");
    headervisible = false;
    sizeContent();
}

$(document).ready(function() {
    sizeContent();
    timelineCron();

//  $("#spinner").tooltipster();
//  $("#checkbar").tooltipster();
});
$(window).resize(function() {
    sizeContent();
});

function sizeContent() {
    // timeline automatisch platzieren
    var additionaloffset = 0;
    var newTop = 75 + additionaloffset;
    $("#timelinewrapper").css({ top: newTop + 'px' });

    var newHeight = window.innerHeight - newTop;
    $("#timelinewrapper").css({ height: newHeight + 'px' });
}


///////////////////////////////

function opentweetdiv() {
    // oeffne schwebendes DIV zum Absenden eines neuen Tweets
    $("#newtweet").toggle("slow");
}

function updaterestzeichen() {
    var restzeichen = 280 - $("#tweetarea").val().length;
    $("#restzeichen").html(restzeichen);
}

function sendtweet() {
    // Tweet absenden!
    $.post("tweet.php", {
        text: $("#tweetarea").val()
    }).done(function(data) {
        if (data != "ok") {
            alert(data);
        }
        else {
            $("#newtweet").hide("slow");
        }
    }).fail(function() {
        alert("Twitter weigert sich zu funktionieren. Versuch' es nachher noch mal. Srykthx.");
    });
}

function fav(tweetid) {
    // Tweet favorisieren!
    $.post("favorite.php", {
        id: tweetid
    }).always(function(data) {
        if (data != "ok") {
            alert("Twitterfehler:\n"+data);
        }
	else {
            $("#fav-"+tweetid).hide("slow");
	}
    });
}
