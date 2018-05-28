
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
            $(".configSect2").hide();
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

        $(".edit").click(function () {
            jQuery(".configSect2").show();
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

        $("#delete_chat").click(function () {
            $(".moreMenu").slideToggle("fast");
            chatloeschenclick();
        });

        $("#send_invitation").click(function () {
            $(".moreMenu").slideToggle("fast");
            sendinvitationclick();
        });

        $(".cn").click(function () {
            window.location.href='./statistic.php';
        });

        $(".nc").click(function () {
            createchatclick();
        });
        
        $(".ng").click(function () {
            // Random-Chat erstellen
            createrandomchatclick();
        });

        jQuery(".alert").each(function (index) {
            jQuery(this).click(function () {
                jQuery(this).hide();
            });
        });

        validateChangeAccountData();

        sessionStorage.aktuelleChatId = 0;
        chatloeschen();
    }

    // Emoji-Click
    $(".pick").click(function() {
        emojiClick($(this).attr('id'));
    });

    if(sessionStorage.chatCreated != null && sessionStorage.chatCreated != 0) {
        // TODO: Ausgabe ändern
        console.log(sessionStorage.chatCreated);
        alert("Chat " + sessionStorage.chatCreated + " erfolgreich erstellt!");
        sessionStorage.chatCreated = 0;
    }

    if(sessionStorage.randomChatCreatedWith != null && sessionStorage.randomChatCreatedWith != 0) {
        // TODO: Ausgabe ändern
        alert("Random-Chat mit " + sessionStorage.randomChatCreatedWith + " erfolgreich erstellt!");
        sessionStorage.randomChatCreatedWith = 0;
    }

    if(typeof linkResult !== 'undefined' && linkResult != null) {
        // TODO: Ausgabe ändern
        alert(linkResult);
    }
});

function validateChangeAccountData() {

    const currentPasswordField = $("#passwordCurrentField");
    const passwordField = $("#passwordChangeField");
    const passwordConfirmField = $("#passwordConfirmChangeField");

    $(".editSubmitButton").click(function (e) {
        const currentPassword = currentPasswordField.val();
        const password = passwordField.val();
        const passwordConfirm = passwordConfirmField.val();

        const passwordMessage = validatePassword(password);

        if (currentPassword === ""){
            e.preventDefault();
            currentPasswordField.addClass("invalid");
            jQuery("#current-password-error").show();
        }

        if (passwordMessage !== "") {
            e.preventDefault();
            console.log(passwordMessage);
            passwordField.addClass("invalid");
            jQuery("#password-error").html(passwordMessage);
            jQuery("#password-error").show();
        }
        if (passwordConfirm !== password) {
            e.preventDefault();
            console.log("password confirm error");
            passwordConfirmField.addClass("invalid");
            jQuery("#password-confirm-error").show();
        }
    });

    currentPasswordField.keypress(function () {
        if(currentPasswordField.hasClass("invalid")) {
            currentPasswordField.removeClass("invalid");
            jQuery("#current-password-error").hide();
        }
    });
    passwordField.keypress(function () {
        if(passwordField.hasClass("invalid")) {
            passwordField.removeClass("invalid");
            jQuery("#password-error").hide();
        }
    });
    passwordConfirmField.keypress(function () {
        if(passwordConfirmField.hasClass("invalid")) {
            passwordConfirmField.removeClass("invalid");
            jQuery("#password-confirm-error").hide();
        }
    });
}

function validatePassword(str) {
    let returnMessage = "";
    if (str.length < 6) {
        returnMessage = returnMessage.concat("<li>must be at least 6 characters</li><br>");
    } if (str.search(/\d/) == -1) {
        returnMessage = returnMessage.concat("<li>must contain at least 1 digit</li><br>");
    } if (str.search(/[A-Z]/) == -1) {
        returnMessage = returnMessage.concat("<li>must contain at least 1 upper case letter</li><br>");
    } if (str.search(/[a-z]/) == -1) {
        returnMessage = returnMessage.concat("<li>must contain at least 1 lower case letter</li><br>");
    } if (str.search(/[!"#\$%&'\(\)\*\+,\-\.\/:;<=>\?@\[\]\^_`{\|}~]/) == -1) {
        returnMessage = returnMessage.concat("<li>must contain at least 1 special character</li>");
    }
    return(returnMessage);
}

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

function replaceEmojis(text) {
    text = text.split(":)").join('<i class="em-svg em-slightly_smiling_face"></i>');
    text = text.split(":\'D").join('<i class="em-svg em-joy"></i>');
    text = text.split(":D").join('<i class="em-svg em-smiley"></i>');
    text = text.split(":(").join('<i class="em-svg em-slightly_frowning_face"></i>');
    text = text.split(":\'(").join('<i class="em-svg em-sob"></i>');
    text = text.split(":o").join('<i class="em-svg em-open_mouth"></i>');
    text = text.split(";)").join('<i class="em-svg em-smirk"></i>');
    text = text.split(":P").join('<i class="em-svg em-stuck_out_tongue"></i>');
    return text;
}

function replaceEmojisBack(id) {
    var emoji = "";
    switch(id) {
        case "smileface":
            emoji = ":)";
            break;
        case "tearjoyface":
            emoji = ":\'D";
            break;
        case "laughingface":
            emoji = ":D";
            break;
        case "sadface":
            emoji = ":(";
            break;
        case "cryingface":
            emoji = ":\'(";
            break;
        case "surpriseface":
            emoji = ":o";
            break;
        case "winkface":
            emoji = ";)";
            break;
        case "cheekyface":
            emoji = ":P";
            break;
    }
    return emoji;
}


function sendChatMsg(chatroomId) {
    var chatText = $("#inputChatMessage").html();

    // Emojis escapen
    if (chatText.length === 0 || !chatText.trim()) { // wenn der String leer ist, oder nur Blanks enthält
        console.log("Nachrichten Text leer oder enthält nur Blanks");
        $("#inputChatMessage").val("");
        $("#inputChatMessage").focus();
    } else {
        //chatText = chatText.replace(/\\/g,"\\\\"); // jeden Backslash escapen, /string/g ersetzt jede Erscheinung von string, sonst nur erste
        //chatText = chatText.replace(/\"/g,"\\\""); // jedes Anführungszeichen escapen

        var jsonSend = '{"i":"sendmessage","chat_id":'+chatroomId+',"msg":"'+chatText+'"}';//{"i":"send-message","chat_id":"14","msg":"Erste Nachricht die Automatisch erstellt wurde!"}
        callChatctlWithSuccess(jsonSend, function (response) {
            console.log(response);
            chatText = replaceEmojis(chatText);

            // Nachricht ausgeben
            $("#chatVerlauf").append("<div class='msg messageSent'>" + chatText + "<span class='timestamp'>" + response + "</span></div>");
            scrollChatVerlauf();
        });
        $("#inputChatMessage").val(""); // löscht den Text aus dem Textfeld
        $("#inputChatMessage").focus();
    }

    //sendReadUntil(sessionStorage.aktuelleChatId);
}

function scrollChatVerlauf() {
    var chathistory = document.getElementById('chatVerlauf');
    chathistory.scrollTop = chathistory.scrollHeight;
}

function logout(){
    var jsonSend = '{"i":"logout"}';
    callChatctl(jsonSend);
    window.location.href='./../logout.php';
}

function chatButtonClick(cid,chatname,members,history) {
    history = replaceEmojis(history);

    $('#chatname').html(chatname);
    $('#mitglieder').html("Mitglieder: "+members);
    $('#chatVerlauf').html(history);
    sessionStorage.aktuelleChatId = cid;
    scrollChatVerlauf();
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

function callChatctlWithSuccess(functionString, successFunction) {
    $.ajax({
        async: true,
        contentType: "application/json",
        url: '../php/ajax_sendmessage.php',
        type : "POST",
        data: functionString,
        dataType: 'json',   //data format
        success: successFunction,
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

function sendinvitationclick() {
    var chat_id = sessionStorage.aktuelleChatId;
    if(chat_id > 0) {
        var getLinkString = '{"i":"getinvitationlink","chat_id":'+chat_id+'}';
        callChatctlWithSuccess(getLinkString, function (response) {
            console.log(response);
            // TODO: Ausgabe ändern
            var location = window.location.hostname+"/link/"+response['link'];
            prompt("Mit diesem Link kannst du Andere in diesen Chat einladen:", "\n\n"+ location);
        });
    }
}

function showlinkresult() {
    // TODO: Ausgabe ändern
    alert('$linkResult');
}

function createchatclick() {
    // TODO: Eingabe des Chat-Namens ändern
    var chatName = prompt("Gib den Chat-Namen ein:", "");

    if(chatName != null && chatName != "") {
        var createChatString = '{"i":"createchat","chat_name":"'+chatName+'"}';
        callChatctlWithSuccess(createChatString, function (response) {
            console.log(response);
            if(response > 0) {
                //$.post("../php/chat_created.php", {"chatName": chatName});
                sessionStorage.chatCreated = chatName;
                //window.location.href = window.location.href;
            }
        });
    }
}

function createrandomchatclick() {
    var createRandomChatString = '{"i":"createrandomchat"}';
    callChatctlWithSuccess(createRandomChatString, function (response) {
        console.log(response);
        if(response != false) {
            sessionStorage.randomChatCreatedWith = response;
            window.location.href = window.location.href;
        }
    });
}

window.setInterval(function () {
    var functionString = '{"i":"ping"}';
    callChatctl(functionString);
},500);



// Emojis-Click
function emojiClick(em) {
    var emoji = replaceEmojisBack(em);
    $("#inputChatMessage").append(emoji);
}
