$(document).ready(function() {
    {/* make side menu show up */
        $(".trigger").click(function () {
            $(".overlay, .menuWrap").fadeIn(180);
            $(".menu").animate({opacity: "1", left: "0px"}, 180);
        });

        /* make config menu show up */
        $(".settings").click(function () {
            $(".config").animate({opacity: "1", right: "0px"}, 180);
            /* hide others */
            $(".menuWrap").fadeOut(180);
            $(".menu").animate({opacity: "0", left: "-320px"}, 180);

        });

        // Show/Hide the other notification options
        $(".deskNotif").click(function () {
            $(".showSName, .showPreview, .playSounds").toggle();
        });

        /* close all overlay elements */
        $(".overlay").click(function () {
            $(".overlay, .menuWrap").fadeOut(180);
            $(".menu").animate({opacity: "0", left: "-320px"}, 180);
            $(".config").animate({opacity: "0", right: "-200vw"}, 180);
        });

        /* small conversation menu */
        $(".otherOptions").click(function () {
            $(".moreMenu").slideToggle("fast");
        });

        /* clicking the search button from the conversation focus the search bar outside it, as on desktop */
        $(".search").click(function () {
            $(".searchChats").focus();
        });

        /**
         * input listener for search field
         * shows/hides chats depending on search input
         * 500ms delay after input changes to prevent multiple fire events
         **/
        var timeout = null;
        $(".searchChats").on("input", function () {
            clearTimeout(timeout);
            timeout = setTimeout(function () {

                var chatList = $(".chats .chatButton");
                var filterString = $(".searchChats").val().toLowerCase().trim();
                if (chatList.length !== 0) {
                    chatList.each(function (index, chat) {
                        var chatName = $(chat).find(".name").text().toLowerCase().trim();
                        if (chatName.indexOf(filterString) >= 0) {
                            $(chat).show();
                        } else {
                            $(chat).hide();
                        }
                    });
                }
            }, 500);
        });

        /* Show or Hide Emoji Panel */
        $(".emoji").click(function () {
            $(".emojiBar").fadeToggle(120);
        });

        /* if the user click the conversation or the type panel will also hide the emoji panel */
        $(".convHistory, .replyMessage").click(function () {
            $(".emojiBar").fadeOut(120);

        });

    }



});

function runScript(e) {
    if (e.keyCode == 13) {
        var tb = document.getElementById("inputChatMessage");
        console.log("Es wurde ENTER gedruckt -  \"" + tb.value + "\"");
        sendChatMsg();
        return false;
    }
}

function sendChatMsg() {
    var chatText = $("#inputChatMessage").val();
    if (chatText.length === 0 || !chatText.trim()) { // wenn der String leer ist, oder nur Blanks enthält
        console.log("Nachrichten Text leer oder enthält nur Blanks");
        $("#inputChatMessage").val("");
        $("#inputChatMessage").focus();
    } else {
        chatText = chatText.replace(/\\/g,"\\\\"); // jeden Backslash escapen, /string/g ersetzt jede Erscheinung von string, sonst nur erste
        chatText = chatText.replace(/\"/g,"\\\""); // jedes Anführungszeichen escapen
        window.alert("Die Nachricht  \""+chatText+"\" wurde gesendet");
        $("#inputChatMessage").val(""); // löscht den Text aus dem Textfeld
        $("#inputChatMessage").focus();
    }

    //sendReadUntil(sessionStorage.aktuelleChatId);
}

function logout(){
    window.location.href='/../logout.php';
}
//$cid[$j],$chatname[$j],$members_2

function chatButtonClick(cid,chatname,members,history) {
    //console.log(cid);
    document.getElementById('chatname').innerHTML = chatname;
    document.getElementById('mitglieder').innerHTML = "Mitglieder: "+members;
    //document.getElementById('chatVerlauf').innerHTML =cid;
    //window.location.href = "../chat/index.php?convHistory=" + cid;
    //window.log(history);
    document.getElementById('chatVerlauf').innerHTML = history;
    /*count = history.length;
    for(var i=0;i<count;i++){
        if(i==0){
            document.getElementById('chatVerlauf').innerHTML = history[i];
        }else document.getElementById('chatVerlauf').innerHTML += history[i];
    }*/
}