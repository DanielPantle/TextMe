$(document).ready(function() {
    {
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

        $(document).mouseup(function(e)
        {
            var container = $(".moreMenu");

            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0)
            {
                container.hide();
            }
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
            $(".nc-div").show();
        });
        $("#nc-abort").click(function () {
            $("#nc-chatnameField").val("");
            $(".nc-div").hide();
        });
        $("#nc-create").click(function () {
            const chatName = $("#nc-chatnameField").val();
            if(chatName != null && chatName != "") {
                createchatclick(chatName);

            }
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
        var functionString = '{"i":"getUserID"}';
        callChatctlWithSuccess(functionString,function(response){
            sessionStorage.aktuelleUserId = response;
        });
        var functionString = '{"i":"getCurrentUser"}';
        callChatctlWithSuccess(functionString,function(response){
            sessionStorage.aktuellerUser = response;
        });
        sessionStorage.lastMessage = 0;
        chatloeschen();
        chats();
        showProfilPicture();

        var functionString = '{"i":"isUserAdmin"}';
        callChatctlWithSuccess(functionString,function(response){
            if(response){
                $(".cn").show();
            }
        });

        // Emoji-Click
        $(".pick").click(function() {
            emojiClick($(this).attr('id'));
        });

        if(typeof linkResult !== 'undefined' && linkResult != null) {
            // TODO: Ausgabe ändern
            alert(linkResult);
        }
    }
});

function setSuccessMessage(message) {
    var messageContainer = $("#messageContainer");
    messageContainer.removeClass();
    messageContainer.addClass("alert alert-success");

    messageContainer.text(message);
    messageContainer.show();
    
    messageContainer.delay(5000).fadeOut();
}

function setErrorMessage(message) {
    var messageContainer = $("#messageContainer");
    messageContainer.removeClass();
    messageContainer.addClass("alert alert-danger");

    messageContainer.text(message);
    messageContainer.show();
}

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
        var cid = parseInt(sessionStorage.aktuelleChatId);
        if(cid!=0){
            sendChatMsg(cid);
        }else console.log ("keine cid");
        return false;
    }
}

function replaceEmojis(text) {
    text = text.split(":)").join('<i class="em-svg em-slightly_smiling_face"></i>');
    text = text.split(":&#039;D").join('<i class="em-svg em-joy"></i>');
    text = text.split(":D").join('<i class="em-svg em-smiley"></i>');
    text = text.split(":(").join('<i class="em-svg em-slightly_frowning_face"></i>');
    text = text.split(":&#039;(").join('<i class="em-svg em-sob"></i>');
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
    var chatText = $("#inputChatMessage").text();
    chatText = chatText.trim();
    if (chatText.length === 0) { // wenn der String leer ist, oder nur Blanks enthält
        console.log("Nachrichten Text leer oder enthält nur Blanks");
        document.getElementById("inputChatMessage").innerText ="";
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
            document.getElementById("inputChatMessage").innerText ="";
            $("#inputChatMessage").focus();
        });

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
        sessionStorage.aktuelleChatId=0;
        chats();
        chatHistory();
        $('#mitglieder').html("");
        $('#chatname').html("");
        $("#nc-chatnameField").val("");
        $(".nc-div").hide();
        setSuccessMessage("Chat successfully deleted!");
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

window.setInterval(function () {
    checkForNewMessagesInCurrentChat();
    var functionString = '{"i":"ping"}';
    callChatctl(functionString);
    lookForUnreadMessages();
},500);

// Emojis-Click
function emojiClick(em) {
    var emoji = replaceEmojisBack(em);
    $("#inputChatMessage").append(emoji);
}

function chatHistory () {
    const chat_id = sessionStorage.aktuelleChatId;
    document.getElementById("chatVerlauf").innerHTML ="";
    if(chat_id>0){
        var functionString = '{"i":"getAllMessagesFromChat","chat_id":"'+chat_id+'"}';
        callChatctlWithSuccess(functionString,function(response){
            addNewMessages(response);
        });
    }else console.log("kein aktueller chat raum");
}

function addNewMessages (response){
    const chat_id = sessionStorage.aktuelleChatId;
    var functionString = '{"i":"getChatUsers","chat_id":"'+chat_id+'"}';
    callChatctlWithSuccess(functionString, function (user) {
        var count = user.length;
        for (var date in response){
            if(document.getElementsByClassName("chatDatum").length==0){
                document.getElementById("chatVerlauf").innerHTML +="<div class=\"chatDatum\">"+date+"</div>";
            }else {
                var date_heute = false;
                $(".chatDatum").each(function () {
                    var date2 = $(this).text();
                    if(date2 == "Heute")date_heute=true;
                });
                if(date_heute==false){
                    document.getElementById("chatVerlauf").innerHTML +="<div class=\"chatDatum\">"+date+"</div>";
                }else if (date!="Heute") {
                    document.getElementById("chatVerlauf").innerHTML +="<div class=\"chatDatum\">"+date+"</div>";
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
                    document.getElementById("chatVerlauf").innerHTML +="<div class=\"chatVerlassen\"><b>"+name+"</b>"+nachricht+"</div>";
                }else if(nachricht==" hat den Chat verlassen"){
                    document.getElementById("chatVerlauf").innerHTML +="<div class=\"chatVerlassen\"><b>"+name+"</b>"+nachricht+"</div>";
                }else {
                    if(username==name){
                        document.getElementById("chatVerlauf").innerHTML +="<div class=\"msg messageSent\">"+nachricht+"<span class=\"timestamp\">"+zeit+"</span></div>";
                    }
                    else if(count>2){
                        var functionString = '{"i":"getUserIdByName","username":"'+name+'"}';
                        callChatctlWithSuccess(functionString,function(userid_members_picture){
                            var functionString = '{"i":"showPictureByUserId","user_id":"'+userid_members_picture+'"}';
                            callChatctlWithSuccess(functionString,function(picture){
                                var count_picture = picture.length;
                                if(count_picture>0){
                                    var bild = picture[0]['imgdata'];
                                    document.getElementById("chatVerlauf").innerHTML +="<div class= \"msgimage\" style=\"background: #FFF url(data:image;base64,"+bild+") no-repeat center;background-size:cover\"></div>";
                                }else {
                                    document.getElementById("chatVerlauf").innerHTML +="<div class= \"msgimage\" style=\"background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover\"></div>";
                                }
                                document.getElementById("chatVerlauf").innerHTML +="<div class=\"msg messageReceivedGroup\">"+name+": "+nachricht+"<span class=\"timestamp\">"+zeit+"</span></div>";
                            });
                        });
                    }else {
                        document.getElementById("chatVerlauf").innerHTML +="<div class=\"msg messageReceived\">"+nachricht+"<span class=\"timestamp\">"+zeit+"</span></div>";
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

function chats() {
    var functionString = '{"i":"getAllChatsFromCurrentUser"}';
    callChatctlWithSuccess(functionString, function (response) {
        document.getElementById("chats").innerHTML ="";
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
                                document.getElementById("chats").innerHTML += "<div class='chatButton' onclick='chatButtonClick("+cid+",\""+members_2+"\");'>\n" +
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
                                    "                    </div>";
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
                                    document.getElementById("chats").innerHTML +="<div class='chatButton' onclick='chatButtonClick("+cid+",\""+members_2+"\",);'>" +
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
                                        "</div>";
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
                                            document.getElementById("chats").innerHTML +="<div class='chatButton' onclick='chatButtonClick("+cid+",\""+members_2+"\",);'>" +
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
                                                "</div>";
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
 * click listener on chats to add class 'active' respectively remove it from other chats
 **/
function addActiveChatClickSwitcher() {
    $(".chatButton").click(function () {
        $("#inputChatMessage").css("display", "block");
        $(".otherTools").css("display", "block");
        jQuery(".active").removeClass("active");
        jQuery(this).addClass("active");
    });
}

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

function showProfilPicture() {
    var functionString = '{"i":"showPictureFromCurrentUser"}';
    callChatctlWithSuccess(functionString,function(response){
        //wenn count großer ist als 0 ist ein bild in der Datenbank vorhanden
        if(response.length>0){
            var bild = response[0]['imgdata'];
            //wenn User ein bild in der Datenbank hat nimmer dies
            //und nutzt es als Hintergrund bild, wenn da ein fehler ist wird das bild Weiß
            document.getElementById("imageplace").innerHTML = '<div class= "image" style="background: #FFF url(data:image;base64,'+bild+') no-repeat center;background-size:cover"></div>';
            document.getElementById("imageplace2").innerHTML = '<div class= "image" style="background: #FFF url(data:image;base64,'+bild+') no-repeat center;background-size:cover"></div>';
        }else {
            //wenn der user das Bild nicht in der Datenbank hat nimmt er ein default bild
            document.getElementById("imageplace").innerHTML = '<div class= "image" style="background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover"></div>';
            document.getElementById("imageplace2").innerHTML = '<div class= "image" style="background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover"></div>';
        }
    });
}