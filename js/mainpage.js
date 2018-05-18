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
        $("#chatOption").click(function () {
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

        /**
         * click listener on chats to add class 'active' respectively remove it from other chats
         **/
        $(".chatButton").click(function () {
            jQuery(".active").removeClass("active");
            jQuery(this).addClass("active");
        });

        /* Show or Hide Emoji Panel */
        $(".emoji").click(function () {
            $(".emojiBar").fadeToggle(120);
        });

        /* if the user click the conversation or the type panel will also hide the emoji panel */
        $(".convHistory, .replyMessage").click(function () {
            $(".emojiBar").fadeOut(120);

        });

        $(".lo").click(function () {
            logout();
        });

        $(".option").click(function () {
            $(".moreMenu").slideToggle("fast");
            chatloeschenclick();
        });

        sessionStorage.aktuelleChatId = 0;
        chatloeschen();
    }


});

function onEnter(e) {
    if (e.keyCode == 13) {
        var tb = document.getElementById("inputChatMessage");
        //console.log("Es wurde ENTER gedruckt -  \"" + tb.value + "\"");
        var cid = parseInt(sessionStorage.aktuelleChatId);
        if(cid!=0){
            console.log("Es wurde-  \"" + tb.value + "\" an "+cid+" verschickt");
            sendChatMsg(cid);
        }else console.log ("keine cid");
        return false;
    }
}

function sendChatMsg(chatroomId) {
    var chatText = $("#inputChatMessage").val();
    if (chatText.length === 0 || !chatText.trim()) { // wenn der String leer ist, oder nur Blanks enthält
        console.log("Nachrichten Text leer oder enthält nur Blanks");
        $("#inputChatMessage").val("");
        $("#inputChatMessage").focus();
    } else {
        chatText = chatText.replace(/\\/g,"\\\\"); // jeden Backslash escapen, /string/g ersetzt jede Erscheinung von string, sonst nur erste
        chatText = chatText.replace(/\"/g,"\\\""); // jedes Anführungszeichen escapen
        var jsonSend = '{"i":"sendmessage","chat_id":'+chatroomId+',"msg":"'+chatText+'"}';//{"i":"send-message","chat_id":"14","msg":"Erste Nachricht die Automatisch erstellt wurde!"}
        callChatctl(jsonSend);
        $("#inputChatMessage").val(""); // löscht den Text aus dem Textfeld
        $("#inputChatMessage").focus();
    }

    //sendReadUntil(sessionStorage.aktuelleChatId);
}

function logout(){
    var jsonSend = '{"i":"logout"}';
    callChatctl(jsonSend);
    window.location.href='./../logout.php';
}

function chatButtonClick(cid,chatname,members,history) {
    $('#chatname').html(chatname);
    $('#mitglieder').html("Mitglieder: "+members);
    $('#chatVerlauf').html(history);
    sessionStorage.aktuelleChatId = cid;
    var chathistory = document.getElementById('chatVerlauf');
    chathistory.scrollTop = chathistory.scrollHeight;
    chatloeschen();
}

function callChatctl(functionString) {
    $.ajax({
        async: true,
        contentType: "application/json",
        url: '../php/ajax_sendmessage.php',
        type : "POST",
        data: functionString,
        dataType: 'json',   //data format
        success: function (response) {
            console.log(response);
        },
        error: function(response, status, error) {
            console.log(response);
            console.log(status);
            console.log(error);
        }
    });
}

function chatloeschen() {
    var chatid = sessionStorage.aktuelleChatId;
    var chatOption = document.getElementById("chatOption")
    if(chatid>0){
        chatOption.style.visibility="visible";
    }else {
        chatOption.style.visibility="hidden";
    }
}

function chatloeschenclick() {
    var chat_id = sessionStorage.aktuelleChatId;
    if(chat_id>0){
        var functionString = '{"i":"chatloeschen","chat_id":'+chat_id+'}';
        callChatctl(functionString);
        var jsonSend = '{"i":"sendmessage","chat_id":'+chat_id+',"msg":" hat den Chat verlassen"}';
        callChatctl(jsonSend);
        window.location="";
    }else console.log("kein aktueller chat raum");
}