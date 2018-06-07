
/**
 * wird ausgeführt, nachdem die Seite geladen ist
 */
$(document).ready(function() {
    
    /**
     * Click-Event des Menü-Buttons in der linken oberen Ecke
     * blendet das Seiten-Menü ein
     */
    $(".trigger").click(function () {
        $(".overlay, .menuWrap").fadeIn(180);
        $(".menu").animate({opacity: "1", left: "0px"}, 180);
    });

    /**
     * Click-Event des Einstellungs-Buttons im Seiten-Menü
     * blendet das Einstellungs-Menü ein
     * blendet das Seiten-Menü aus
     */
    $(".settings").click(function () {
        $(".config").animate({opacity: "1", right: "0px"}, 180);
        /* hide others */
        $(".menuWrap").fadeOut(180);
        $(".menu").animate({opacity: "0", left: "-320px"}, 180);

    });

    /* TODO: Kann gelöscht werden?
    // Show/Hide the other notification options
    $(".deskNotif").click(function () {
        $(".showSName, .showPreview, .playSounds").toggle();
    });
    */

    /**
     * Click-Event des Overlays neben dem Seiten-Menü
     * blendet das Seiten-Menü und das Einstellungs-Menü aus
     */
    $(".overlay").click(function () {

        var passwordCurrentField = $("#passwordCurrentField");
        var passwordChangeField = $("#passwordChangeField");
        var passwordConfirmChangeField = $("#passwordConfirmChangeField");

        $(".nc-div").hide();
        passwordCurrentField.val("");
        passwordChangeField.val("");
        passwordConfirmChangeField.val("");

        passwordCurrentField.removeClass("invalid");
        passwordChangeField.removeClass("invalid");
        passwordConfirmChangeField.removeClass("invalid");

        $("#current-password-error").hide();
        $("#password-error").hide();
        $("#password-confirm-error").hide();

        $(".configSect2").hide();
        $(".overlay, .menuWrap").fadeOut(180);
        $(".menu").animate({opacity: "0", left: "-320px"}, 180);
        $(".config").animate({opacity: "0", right: "-200vw"}, 180);
    });

    /**
     * Click-Event des Chat-Menü-Buttons
     * blendet das Chat-Menü ein
     */
    $("#chatOption").click(function () {
        $(".moreMenu").slideToggle("fast");
    });


    /**
     * Click-Event des gesamten Dokuments
     * wenn das Chat-Menü geöffnet ist, wird dieses geschlossen
     */
    $(document).mouseup(function(e)
    {
        var container = $(".moreMenu");

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0)
        {
            container.hide();
        }
    });

    /* TODO: kann gelöscht werden?
    /* clicking the search button from the conversation focus the search bar outside it, as on desktop
    $(".search").click(function () {
        $(".searchChats").focus();
    });
    */

    /**
     * Input-Event für das Suchfeld
     * Blendet Chats ein/aus, je nach Eingabe im Suchfeld
     * 500ms Wartezeit, um mehrfaches Event zu vermeiden
     */
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
     * Click-Event des Passwort-bearbeiten-Buttons
     * blendet das Passwort-bearbeiten-Feld ein
     */
    $(".edit").click(function () {
        $(".configSect2").show();
    });

    /**
     * Click-Event auf den Emoji-Button
     * blendet das Emoji-Feld ein
     */
    /* Show or Hide Emoji Panel */
    $(".emoji").click(function () {
        $(".emojiBar").fadeToggle(120);
    });

    /**
     * Click-Event auf die Chat-Anzeige und das Eingabefeld
     * blendet das Emoji-Feld aus
     */
    $(".convHistory, .replyMessage").click(function () {
        $(".emojiBar").fadeOut(120);
    });

    /**
     * Click-Event auf den Logout-Button
     * ruft die logout-Funktion auf
     */
    $(".lo").click(function () {
        logout();
    });


    /**
     * Click-Event auf den Chat-Löschen-Button
     * blendet das Chat-Menü aus
     * ruft die Chat-Löschen-Funktion auf
     */
    $("#delete_chat").click(function () {
        $(".moreMenu").slideToggle("fast");
        chatloeschenclick();
    });

    /**
     * Click-Event auf den Einladung-Versenden-Button
     * blendet das Chat-Menü aus
     * ruft die Einladung-Versenden-Funktion auf
     */
    $("#send_invitation").click(function () {
        $(".moreMenu").slideToggle("fast");
        sendinvitationclick();
    });

    /**
     * Click-Event auf den Statistik-Button
     * leitet auf die Statistik-Seite weiter
     */
    $(".cn").click(function () {
        window.location.href="./statistic.php";
    });

    /**
     * Click-Event auf den Neuer-Chat-Button
     * blendet das Menü ein, um einen neuen Chat zu erstellen
     */
    $(".nc").click(function () {
        $(".nc-div").show();
    });


    /**
     * Click-Event auf den Abbrechen-Button (bei der Erstellung eines neuen Chats)
     * löscht das Eingabe-Feld des Chat-Namens
     * blendet das Menü aus, um einen neuen Chat zu erstellen
     */
    $("#nc-abort").click(function () {
        $("#nc-chatnameField").val("");
        $(".nc-div").hide();
    });

    /**
     * Click-Event auf den Erstellen-Button (bei der Erstellung eines neuen Chats)
     * überprüft den eingegebenen Chat-Namen
     * ruft die Chat-Erstellen-Funktion auf (Parameter: eingegebener Chat-Name)
     */
    $("#nc-create").click(function () {
        const chatName = $("#nc-chatnameField").val();
        if(chatName != null && chatName != "") {
            createchatclick(chatName);
        }
    });
    
    /**
     * Click-Event auf den Random-Chat-Erstellen-Button
     * ruft die Random-Chat-Erstellen-Funktion auf
     */
    $(".ng").click(function () {
        // Random-Chat erstellen
        createrandomchatclick();
    });

    /**
     * Click-Event auf ein Alert
     * blendet das Alert aus
     */
    $(".alert").click(function(index) {
        $(this).hide();
    });

    /**
     * Server-Abfrage, ob angemeldeter User Admin ist
     * wenn ja: Statistik-Button wird angezeigt
     */
    var functionString = '{"i":"isUserAdmin"}';
    callChatctlWithSuccess(functionString,function(response){
        if(response>0){
            $(".cn").show();
        }
    });

    // Speichert die ID des Users in einer Session-Variable
    sessionStorage.aktuelleChatId = 0;
    functionString = '{"i":"getUserID"}';
    callChatctlWithSuccess(functionString,function(response){
        sessionStorage.aktuelleUserId = response;
    });

    // Speichert den Namen des Users in einer Session-Variable
    functionString = '{"i":"getCurrentUser"}';
    callChatctlWithSuccess(functionString,function(response){
        sessionStorage.aktuellerUser = response;
    });

    // Speichert die E-Mail-Adresse des Users in einer Session-Variable
    functionString = '{"i":"getEmail","user_id":'+sessionStorage.aktuelleUserId+'}';
    callChatctlWithSuccess(functionString,function(response){
        sessionStorage.aktuelleeEmail = response;
    });

    // Setzt den Wert der Session-Variable auf 0
    sessionStorage.lastMessage = 0;

    // Ruft Funktionen auf, um die Chats anzuzeigen
    chatloeschen();
    chats();
    showProfilPicture();
    showProfilInfos();
    validateChangeAccountData();


    /**
     * Click-Event auf ein Emoji im Emoji-Feld
     * ruft die Funktion Emoji-Click auf (Parameter: id des geklickten Emojis)
     */
    $(".pick").click(function() {
        emojiClick($(this).attr('id'));
    });

    // Gibt aus, ob ein Link geklickt wurde
    if(typeof linkResult !== "undefined" && linkResult != null) {
        /* TODO: Ausgabe ändern */
        alert(linkResult);
    }
});

/**
 * Zeigt eine erfolgreiche Ausgabe mit einer übergebenen Nachricht
 * @param message
 */
function setSuccessMessage(message) {
    var messageContainer = $("#messageContainer");
    messageContainer.removeClass();
    messageContainer.addClass("alert alert-success");

    messageContainer.text(message);
    messageContainer.show();

    messageContainer.delay(5000).fadeOut();
}

/**
 * Zeigt eine fehlgeschlagene Ausgabe mit einer übergebenen Nachricht
 * @param message
 */
function setErrorMessage(message) {
    var messageContainer = $("#messageContainer");
    messageContainer.removeClass();
    messageContainer.addClass("alert alert-danger");

    messageContainer.text(message);
    messageContainer.show();
}


/**
 * Validiert die Eingabe beim Speichern bei der Passwort-Änderung
 */
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
            $("#current-password-error").show();
        }

        if (passwordMessage !== "") {
            e.preventDefault();
            console.log(passwordMessage);
            passwordField.addClass("invalid");
            $("#password-error").html(passwordMessage);
            $("#password-error").show();
        }
        if (passwordConfirm !== password) {
            e.preventDefault();
            console.log("password confirm error");
            passwordConfirmField.addClass("invalid");
            $("#password-confirm-error").show();
        }
    });

    currentPasswordField.keypress(function () {
        if(currentPasswordField.hasClass("invalid")) {
            currentPasswordField.removeClass("invalid");
            $("#current-password-error").hide();
        }
    });
    passwordField.keypress(function () {
        if(passwordField.hasClass("invalid")) {
            passwordField.removeClass("invalid");
            $("#password-error").hide();
        }
    });
    passwordConfirmField.keypress(function () {
        if(passwordConfirmField.hasClass("invalid")) {
            passwordConfirmField.removeClass("invalid");
            $("#password-confirm-error").hide();
        }
    });
}

/**
 * Validiert das übergebene Passwort
 * @param str
 */
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

/**
 * Eingabe-Event für die Enter-Taste
 * wird die Enter-Taste gedrückt und ein Chat ist ausgewählt, wird die Nachricht gesendet
 */
function onEnter(e) {
    if (e.keyCode == 13) {
        var cid = parseInt(sessionStorage.aktuelleChatId);
        if(cid!=0){
            sendChatMsg(cid);
        }else {
            console.log ("keine cid");
        }
        return false;
    }
}

/**
 * ersetzt die Emojis aus einer Nachricht mit einem Symbol
 * @param text
 */
function replaceEmojis(text) {
    text = text.split(":)").join("<i class='em-svg em-slightly_smiling_face'></i>");
    text = text.split(":&#039;D").join("<i class='em-svg em-joy'></i>");
    text = text.split(":D").join("<i class='em-svg em-smiley'></i>");
    text = text.split(":(").join("<i class='em-svg em-slightly_frowning_face'></i>");
    text = text.split(":&#039;(").join("<i class='em-svg em-sob'></i>");
    text = text.split(":o").join("<i class='em-svg em-open_mouth'></i>");
    text = text.split(";)").join("<i class='em-svg em-smirk'></i>");
    text = text.split(":P").join("<i class='em-svg em-stuck_out_tongue'></i>");
    text = text.split(":flo:").join("<i class='em-svg em-middle_finger'></i>");

    return text;
}

/**
 * gibt das Emoji zurück, das angeklickt wurde
 * @param id
 */
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
        case "flo":
            emoji = ":flo:";
            break;
    }
    return emoji;
}

/**
 * Sendet eine Chat-Nachricht an den Server
 * @param chatroomId
 */
function sendChatMsg(chatroomId) {
    var chatText = $("#inputChatMessage").text();
    chatText = chatText.trim();
    if (chatText.length === 0) { // wenn der String leer ist, oder nur Blanks enthält
        console.log("Nachrichten Text leer oder enthält nur Blanks");
        $("#inputChatMessage").html("");
        $("#inputChatMessage").focus();
    } else {
        //chatText = chatText.replace(/\\/g,"\\\\"); // jeden Backslash escapen, /string/g ersetzt jede Erscheinung von string, sonst nur erste
        chatText = chatText.trim();
        chatText = chatText.replace(/\"/g,"\\\""); // jedes Anführungszeichen escapen
        var jsonSend = '{"i":"sendmessage","chat_id":'+chatroomId+',"msg":"'+chatText+'"}';
        callChatctlWithSuccess(jsonSend, function (response) {
            chatText = replaceEmojis(chatText);
            // Nachricht ausgeben
            //$("#chatVerlauf").append("<div class='msg messageSent'>" + chatText + "<span class='timestamp'>" + response + "</span></div>");
            var functionString = '{"i":"setFlagUnreadMessageForEveryOne","chat_id":"'+sessionStorage.aktuelleChatId+'","user_id":"'+sessionStorage.aktuelleUserId+'","setbit":"1"}';
            callChatctl(functionString);
            scrollChatVerlauf();
            $("#inputChatMessage").html("");
            $("#inputChatMessage").focus();
        });
    }
}

/**
 * scrollt den Chat-Verlauf nach ganz unten
 */
function scrollChatVerlauf() {
    $("#chatVerlauf").scrollTop($("#chatVerlauf").prop("scrollHeight"));
}

/**
 * sendet eine Logout-Nachricht an den Server und leitet an die Logout-Seite weiter
 */
function logout(){
    var jsonSend = '{"i":"logout"}';
    callChatctl(jsonSend);
    window.location.href='./../logout.php';
}

/**
 * wird aufgerufen bei Klick auf einen Chat
 * fragt den Chat-Namen vom Server ab und gibt ihn aus
 * ruft die Funktion chatHistory auf, die die Nachrichten vom Server ab
 * @param cid
 * @param members
 */
function chatButtonClick(cid,members) {
    sessionStorage.aktuelleChatId = cid;
    var functionString = '{"i":"getChatnameById","chat_id":'+cid+'}';
    callChatctlWithSuccess(functionString, function (chatname) {
        $('#chatname').html(chatname);
    });
    $('#mitglieder').html("Mitglieder: "+members);
    chatHistory();
    chatloeschen();
}

/**
 * sendet die übergebene Nachricht an den Server
 * gibt das Ergebnis in der Konsole aus
 * @param functionString
 */
function callChatctl(functionString) {
    $.ajax({
        async: true,
        contentType: "application/json",
        url: "../php/ajax_sendmessage.php",
        type : "POST",
        data: functionString,
        dataType: "json",   //data format
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

/**
 * sendet die übergebene Nachricht an den Server
 * ruft die übergebene Funktion auf, wenn der Aufruf erfolgreich war
 * @param functionString
 * @param successFunction
 */
function callChatctlWithSuccess(functionString, successFunction) {
    $.ajax({
        async: false,
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

/**
 * löscht einen Chat aus der Ausgabe
 */
function chatloeschen() {
    var chatid = sessionStorage.aktuelleChatId;
    if(chatid>0){
        $("#chatOption").css("visibility", "visible");
    }else {
        $("#chatOption").css("visibility", "hidden");
    }
}

/**
 * sendet eine Chat-Löschen-Nachricht an den Server
 */
function chatloeschenclick() {
    var chat_id = sessionStorage.aktuelleChatId;
    if(chat_id>0){
        var functionString = '{"i":"chatloeschen","chat_id":'+chat_id+'}';
        callChatctl(functionString);
        var jsonSend = '{"i":"sendmessage","chat_id":'+chat_id+',"msg":" hat den Chat verlassen"}';
        callChatctl(jsonSend);
        sessionStorage.aktuelleChatId=0;
        chats();
        chatHistory();
        $("#mitglieder").html("");
        $("#chatname").html("");
        $("#nc-chatnameField").val("");
        $(".nc-div").hide();
        setSuccessMessage("Chat successfully deleted!");
    }else console.log("kein aktueller chat raum");
}

/**
 * fragt den Einladungs-Link vom Server ab und gibt ihn aus
 */
function sendinvitationclick() {
    var chat_id = sessionStorage.aktuelleChatId;
    if(chat_id > 0) {
        var getLinkString = '{"i":"getinvitationlink","chat_id":'+chat_id+'}';
        callChatctlWithSuccess(getLinkString, function (response) {
            console.log(response);
            /* TODO: Ausgabe ändern */
            var location = window.location.hostname+"/link/"+response['link'];
            prompt("Mit diesem Link kannst du Andere in diesen Chat einladen:", "\n\n"+ location);
        });
    }
}

/**
 * sendet eine Chat-Erstellen-Nachricht an den Server
 * bei erfolgreicher Erstellung wird der Chat hinzugefügt
 */
function createchatclick(chatName) {
    var chatName = chatName;

        var createChatString = '{"i":"createchat","chat_name":"'+chatName+'"}';
        callChatctlWithSuccess(createChatString, function (response) {
            console.log(response);
            if(response > 0) {
                chats();
                $(".overlay, .menuWrap").fadeOut(180);
                var message = "Chat: " + chatName + " erfolgreich erstellt!"
                setSuccessMessage(message)
            }
        });
}

/**
 * sendet eine Random-Chat-Erstellen-Nachricht an den Server
 * bei erfolgreicher Erstellung wird der Random-Chat hinzugefügt
 */
function createrandomchatclick() {
    var createRandomChatString = '{"i":"createrandomchat"}';
    callChatctlWithSuccess(createRandomChatString, function (response) {
        console.log(response);
        if(response != false) {
            chats();
            $(".overlay, .menuWrap").fadeOut(180);;
            var message = "Random-Chat mit " + response + " erfolgreich erstellt!";
            setSuccessMessage(message);
        }
    });
}

/**
 * Timer, der alle 500ms feuert
 * fragt ab, ob neue Nachrichten in dem aktuellen Chat gesendet wurden
 * fragt ab, ob neue Nachrichten in einem anderen Chat gesendet wurden
 */
window.setInterval(function () {
    checkForNewMessagesInCurrentChat();
    var functionString = '{"i":"ping"}';
    callChatctl(functionString);
    lookForUnreadMessages();
},500);

/**
 * fragt das geklickte Emoji ab und hängt es im Eingabe-Feld an die eingegebene Nachricht an
 * @param em
 */
function emojiClick(em) {
    var emoji = replaceEmojisBack(em);
    $("#inputChatMessage").append(emoji);
}

/**
 * fragt alle Nachrichten aus dem aktuellen Chat vom Server ab
 */
function chatHistory () {
    const chat_id = sessionStorage.aktuelleChatId;
    $("#chatVerlauf").html("");
    if(chat_id>0){
        var functionString = '{"i":"getAllMessagesFromChat","chat_id":"'+chat_id+'"}';
        callChatctlWithSuccess(functionString,function(response){
            addNewMessages(response);
        });
    }else console.log("kein aktueller chat raum");
}

/**
 * gibt  die vom Server geladenen Nachrichten aus
 * @param response
 */
function addNewMessages (response){
    const chat_id = sessionStorage.aktuelleChatId;
    var functionString = '{"i":"getChatUsers","chat_id":"'+chat_id+'"}';
    callChatctlWithSuccess(functionString, function (user) {
        var count = user.length;
        for (var date in response){
            if($(".chatDatum").length==0){
                $("#chatVerlauf").append("<div class=\"chatDatum\">"+date+"</div>");
            }else {
                var date_heute = false;
                $(".chatDatum").each(function () {
                    var date2 = $(this).text();
                    if(date2 == "Heute")date_heute=true;
                });
                if(date_heute==false){
                    $("#chatVerlauf").append("<div class=\"chatDatum\">"+date+"</div>");
                }else if (date!="Heute") {
                    $("#chatVerlauf").append("<div class=\"chatDatum\">"+date+"</div>");
                }
            }
            for(var i=0;i<response[date].length;i++){
                var obj = response[date][i];
                var nachricht_1 = obj['message'];
                const nachricht = replaceEmojis(nachricht_1);
                const zeit = obj['time'];
                const name = obj['name'];
                const mid = obj['mid'];
                sessionStorage.lastMessage = mid;
                //console.log("i: "+i+" mid: "+mid);
                //console.log("message:"+nachricht);
                var username = sessionStorage.aktuellerUser;
                if(nachricht==" wurde dem Chat hinzugefuegt"){
                    $("#chatVerlauf").append("<div class=\"chatVerlassen\"><b>"+name+"</b>"+nachricht+"</div>");
                }else if(nachricht==" hat den Chat verlassen"){
                    $("#chatVerlauf").append("<div class=\"chatVerlassen\"><b>"+name+"</b>"+nachricht+"</div>");
                }else {
                    if(username==name){
                        $("#chatVerlauf").append("<div class=\"msg messageSent\">"+nachricht+"<span class=\"timestamp\">"+zeit+"</span></div>");
                    }
                    else if(count>2){
                        var functionString = '{"i":"getUserIdByName","username":"'+name+'"}';
                        callChatctlWithSuccess(functionString,function(userid_members_picture){
                            var functionString = '{"i":"showPictureByUserId","user_id":"'+userid_members_picture+'"}';
                            callChatctlWithSuccess(functionString,function(picture){
                                var count_picture = picture.length;
                                if(count_picture>0){
                                    var bild = picture[0]['imgdata'];
                                    $("#chatVerlauf").append("<div class= \"msgimage\" style=\"background: #FFF url(data:image;base64,"+bild+") no-repeat center;background-size:cover\"></div>");
                                }else {
                                    $("#chatVerlauf").append("<div class= \"msgimage\" style=\"background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover\"></div>");
                                }
                                $("#chatVerlauf").append("<div class=\"msg messageReceivedGroup\">"+name+": "+nachricht+"<span class=\"timestamp\">"+zeit+"</span></div>");
                            });
                        });
                    }else {
                        $("#chatVerlauf").append("<div class=\"msg messageReceived\">"+nachricht+"<span class=\"timestamp\">"+zeit+"</span></div>");
                    }
                }
            }
        }
        var functionString = '{"i":"setFlagUnreadMessage","chat_id":"'+sessionStorage.aktuelleChatId+'","user_id":"'+sessionStorage.aktuelleUserId+'"}';
        callChatctl(functionString);
        scrollChatVerlauf();
        $(".status").each(function () {
            //console.log("cid2: "+$(this).data("cid"));
            var cid2 = $(this).data("cid");
            if(chat_id==cid2){
                $(this).hide();
            }
        });
    });
}

/**
 * Liest alle Chats vom Server und gibt diese aus
 */
function chats() {
    var functionString = '{"i":"getAllChatsFromCurrentUser"}';
    callChatctlWithSuccess(functionString, function (response) {
        $("#chats").html("");
        const count = response.length;
        if(count>0){
            for(var j = 0; j<count;j++){
                const obj = response[j];
                const cid = obj['cid'];
                functionString='{"i":"isChatDeletedForUser","chat_id":"'+cid+'"}';
                callChatctlWithSuccess(functionString,function (deleted) {
                    if (deleted<1){
                        var chatname = obj['chatname'];
                        functionString='{"i":"getChatUsers","chat_id":"'+cid+'"}';
                        var members_2 ="";
                        callChatctlWithSuccess(functionString,function(members){
                            var count_members = members.length;
                            var members_2="";
                            var members_10;
                            var members_20;
                            if(count_members>1){
                                members_10 = members[0]['name'];
                                members_20 = members[1]['name'];
                            }else {
                                members_10 = members[0]['name'];
                                members_20 = "NV";
                            }
                            for (var i=0;i<count_members;i++){
                                if(members_2==""){
                                    members_2 = members[i]['name'];
                                }else members_2 = members_2+" , "+members[i]['name'];
                            }
                            if(count_members>2){
                                $("#chats").append("<div class='chatButton' onclick='chatButtonClick("+cid+",\""+members_2+"\");'>\n" +
                                    "                    <div class='chatInfo'>\n" +
                                    "                        <div class='image'>\n" +
                                    "                        </div>\n" +
                                    "                        <p class='name'>\n" +
                                    "                        "+chatname+"\n" +
                                    "                        </p>\n" +
                                    "                        <p class='message'>\n" +
                                    "                        Mitglieder: "+members_2+"\n" +
                                    "                    </p>\n" +
                                    //"                    </div>\n" +
                                    "<div class=\"status onTop\" data-cid=\""+cid+"\" style='display: none'><p class=\"newMessage\">!</p></div>"+
                                    "                    </div>\n" +
                                    "                    </div>");
                            }else {
                                var members_picture="";
                                if(members_20=="NV"){
                                    var members_0=">kein Chat Partner vorhanden<";
                                }else {
                                    if(members_10==sessionStorage.aktuellerUser){
                                        members_0 = "An: "+members_20;
                                        members_picture = members_20;
                                    }else {
                                        members_0 = "An: "+members_10;
                                        members_picture = members_10;
                                    }
                                }
                                var image ="";
                                if(members_picture ==""){
                                    $("#chats").append("<div class='chatButton' onclick='chatButtonClick("+cid+",\""+members_2+"\",);'>" +
                                        "<div class='chatInfo'>" +
                                        "<p class='name'>" +
                                        ""+chatname+
                                        "</p>" +
                                        "<p class='message'>" +
                                        ""+members_0+
                                        "</p>" +
                                        //"</div>" +
                                        "<div class=\"status onTop\" data-cid=\""+cid+"\" style='display: none'><p class=\"newMessage\">!</p></div>"+
                                        "</div>" +
                                        "</div>");
                                }else{
                                    functionString='{"i":"getUserIdByName","username":"'+members_picture+'"}';
                                    callChatctlWithSuccess(functionString,function (userid_members_picutre) {
                                        functionString='{"i":"showPictureByUserId","user_id":"'+userid_members_picutre+'"}';
                                        callChatctlWithSuccess(functionString,function (picture) {
                                            var count_picture = picture.length;
                                            if(count_picture>0){
                                                let bild = picture[0]['imgdata'];
                                                image =  '<div class= "imagenew" style="background: #FFF url(data:image;base64,'+ bild+') no-repeat center;background-size:cover"></div>';
                                            }else {
                                                image = '<div class= "imagenew" style="background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover"></div>';
                                            }
                                            $("#chats").append("<div class='chatButton' onclick='chatButtonClick("+cid+",\""+members_2+"\",);'>" +
                                                ""+image+
                                                "<div class='chatInfo'>" +
                                                "<p class='name'>" +
                                                ""+chatname+
                                                "</p>" +
                                                "<p class='message'>" +
                                                ""+members_0+
                                                "</p>" +
                                                //"</div>" +
                                                "<div class=\"status onTop\" data-cid=\""+cid+"\" style='display: none' ><p class=\"newMessage\">!</p></div>"+
                                                "</div>" +
                                                "</div>");
                                        });
                                    });
                                }
                            }
                        });
                    }
                });
            }
        }
    });
    addActiveChatClickSwitcher();
}

/**
 * prüft, ob im aktuellen Chat neue Nachrichten gesendet wurden
 */
function checkForNewMessagesInCurrentChat() {
    const chat_id = sessionStorage.aktuelleChatId;
    if (chat_id>0){
        if(sessionStorage.lastMessage>0){
            var functionString = '{"i":"getLastMessageIdFromChat","chat_id":"'+chat_id+'"}';
            callChatctlWithSuccess(functionString, function (response) {
                if(response == sessionStorage.lastMessage){}else {
                    var functionString = '{"i":"getNewMessages","chat_id":"'+chat_id+'","message_id":"'+sessionStorage.lastMessage+'"}';
                    callChatctlWithSuccess(functionString, function (response) {
                        addNewMessages(response);
                    });
                }
            });
        }
    }
}
/**
 * Click-Event auf einen Button
 * der aktuelle Chat wird als aktiv markiert
 */
function addActiveChatClickSwitcher() {
    $(".chatButton").click(function () {
        $("#inputChatMessage").css("display", "block");
        $(".otherTools").css("display", "block");
        $(".active").removeClass("active");
        $(this).addClass("active");
    });
}

/**
 * prüft, ob in einem nicht-aktiven Chat neue Nachrichten gesendet wurden
 */
function lookForUnreadMessages () {
    var functionString = '{"i":"proofForNewMessages"}';
    callChatctlWithSuccess(functionString,function(response){
        if(response.length > 0){
            for(var i=0;i<response.length;i++){
                var cid = response[i]['cid'];
                $(".status").each(function () {
                    var cid2 = $(this).data("cid");
                    if(cid==cid2){
                        $(this).show();
                    }
                });
            }
        }
    });
}

/**
 * zeigt das Profilbild an
 */
function showProfilPicture() {
    var functionString = '{"i":"showPictureFromCurrentUser"}';
    callChatctlWithSuccess(functionString,function(response){
        //wenn count großer ist als 0 ist ein bild in der Datenbank vorhanden
        if(response.length>0){
            var bild = response[0]['imgdata'];
            //wenn User ein bild in der Datenbank hat nimmer dies
            //und nutzt es als Hintergrund bild, wenn da ein fehler ist wird das bild Weiß
            $("#imageplace").html('<div class= "image" style="background: #FFF url(data:image;base64,'+bild+') no-repeat center;background-size:cover"></div>');
            $("#imageplace2").html('<div class= "image" style="background: #FFF url(data:image;base64,'+bild+') no-repeat center;background-size:cover"></div>');
        }else {
            //wenn der user das Bild nicht in der Datenbank hat nimmt er ein default bild
            $("#imageplace").html('<div class= "image" style="background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover"></div>');
            $("#imageplace2").html('<div class= "image" style="background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover"></div>');
        }
    });
}

/**
 * gibt die Profil-Infos aus
 */
function showProfilInfos() {
    $('#nutzernamen').html(sessionStorage.aktuellerUser);
    $('#nutzernamen2').html(sessionStorage.aktuellerUser);
    $('#email').html(sessionStorage.aktuelleeEmail);
    $('#email2').html(sessionStorage.aktuelleeEmail);
}
