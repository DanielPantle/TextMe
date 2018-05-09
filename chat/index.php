<!DOCTYPE html>
<html lang="en">
<head>
<?php

include("../php/db.php");
$Database = new Database();

$username;
// Login überprüfen
if(isset($_POST['login-submit'])) {
    // Login-Form wurde abgesendet
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Einloggen
    if($Database->login($username, $password)) {
        ?>
        <script>console.log("Login hat geklappt")
        </script>
        <?php
    }


}

// prüfen, ob User eingeloggt ist
if(!$Database->isLoggedIn()) {
    // zur Login-Seite weiterleiten
    header('Location: /../');
}
//print_r($chats);
?>


    <meta charset="UTF-8">
    <title>TextMe - Pineapple</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/Icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mainpage.css">
</head>
<body>
<!-- -------- -->
<!-- MAIN APP -->
<!-- -------- -->
<section class="mainApp">
    <div class="leftPanel">
        <!--Bereich oben links Menü und suchleiste-->
        <header>
            <button class="trigger">
                <i class="material-icons">&#xE5D2;</i>
            </button>
            <input class="searchChats" type="search" placeholder="Search..."/>
        </header>
        <!-- Bereich links Chats auflistung-->
        <div class="chats">
            <?php
                $chats = $Database->getAllChatsFromCurrentUser();
                $count = count($chats);
                //echo $count;
                if ($count > 0){
                    echo "<br>";
                    for ($j=0;$j<$count;$j++){
                        $cid[$j]=$chats[$j]['cid'];
                        $chatname[$j] =$chats[$j]['chatname'];
                        $members[$j] =$chats[$j]['members'];
                        $members[$j] = explode(',',$members[$j]);
                        $count_2 = count($members[$j]);
                        //echo "Chat_Id: ".$cid[$j]." Chatname: ".$chatname[$j]." Members: ";
                        $members_2="";
                        for ($i=0;$i<$count_2;$i++){
                            if($members_2==""){
                                $members_2 = $members[$j][$i];
                            }else $members_2 =$members_2." , ".$members[$j][$i];

                        }
                        echo "<div class='chatButton'>
                                <div class='chatInfo'>
                                    <div class='image'>
                                        
                                    </div>
                                    <p class='name'>
                                        $chatname[$j]                        
                                    </p>
                                    <p class='message'>
                                        Mitglieder: $members_2
                                    </p>
                                </div>
                              </div>
                        ";
                    }
                }
                ?>

        </div>
    </div>
    <!-- Rechte Seite der Seite in JS -->
    <div class="rightPanel">
        <!-- obere Leiste des Chats -->
        <div class="topBar">
            <div class="rightSide">
                <button class="tbButton search">
                    <i class="material-icons">&#xE8B6;</i>
                </button>
                <button class="tbButton otherOptions">
                    <i class="material-icons">more_vert</i>
                </button>
            </div>

            <div class="leftSide">
                <p class="chatName">Doge</p>
                <p class="chatStatus">Online</p>
            </div>
        </div>
        <!-- Chat verlauf -->
        <div class="convHistory userBg">
            <!-- CONVERSATION GOES HERE! -->

            <div class="msg messageReceived">
                Wow!
                <span class="timestamp">00:00</span>
            </div>

            <div class="msg messageSent">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore fuga cumque aut, harum mollitia aperiam similique dolore voluptates reprehenderit, reiciendis ipsum totam, assumenda autem explicabo amet dolorum eveniet vero delectus?
                <i class="material-icons readStatus">done_all</i>
                <span class="timestamp">00:01</span>
            </div>

            <div class="msg messageReceived">
                Wow!
                <span class="timestamp">00:02</span>
            </div>

            <div class="msg messageReceived">
                Wow!
                <span class="timestamp">00:02</span>
            </div>

            <div class="msg messageReceived">
                Wow!
                <span class="timestamp">00:02</span>
            </div>

            <div class="msg messageReceived">
                Wow!
                <span class="timestamp">00:02</span>
            </div>

            <div class="msg messageSent">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore fuga cumque aut, harum mollitia aperiam similique dolore voluptates reprehenderit, reiciendis ipsum totam, assumenda autem explicabo amet dolorum eveniet vero delectus?
                <i class="material-icons readStatus">done</i>
                <span class="timestamp">00:04</span>
            </div>
        </div>
        <!-- Leiste unter dem Chat -->
        <div class="replyBar">

            <!-- Anhang kann meiner Meinung erstmal weg -->
            <!--<button class="attach">
                <i class="material-icons d45">attach_file</i>
            </button>-->
            <!-- Eingabefeld -->
            <input id="inputChatMessage" type="text" onkeypress="return runScript(event)" class="replyMessage" placeholder="Type your message..."/>

            <div class="emojiBar">
                <!-- Emoji's /Stickers / GIF -->
                <div class="emoticonType">
                    <button id="panelEmoji">Emojis</button>
                    <!-- kann erstmal weg -->
                    <!--<button id="panelStickers">Stickers</button>
                    <button id="panelGIFs">GIFs</button>-->
                </div>

                <!-- Emoji panel -->
                <div class="emojiList">
                    <button id="smileface" class="pick"></button>
                    <button id="grinningface" class="pick"></button>
                    <button id="tearjoyface" class="pick"></button>
                    <button id="rofl" class="pick"></button>
                    <button id="somface" class="pick"></button>
                    <button id="swfface" class="pick"></button>
                </div>

                <!-- the best part of telegram ever: STICKERS!! -->
                <!--<div class="stickerList">
                    <button id="smileface" class="pick"></button>
                    <button id="grinningface" class="pick"></button>
                    <button id="tearjoyface" class="pick"></button>
                </div>-->
            </div>

            <div class="otherTools">
                <button class="toolButtons emoji">
                    <i class="material-icons">face</i>
                </button>

                <button class="toolButtons audio">
                    <i class="material-icons">mic</i>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- ---------------------- -->
<!-- MENU AND OVERLAY STUFF -->
<!-- ---------------------- -->

<!-- MENU -->
<section class="menuWrap">
    <div class="menu">
        <!-- menu oben -->
        <div class="me userBg">
            <div class="image"></div>

            <div class="myinfo">
                <p class="name" id="nutzernamen">Random Name</p>
                <p id="email"> eMail Adresse?</p>
                <!--<p class="phone">+1 12 1234 5678</p>-->
            </div>

            <!--<button class="cloud">
                <i class="material-icons">cloud</i>
            </button>-->

            <button class="settings">
                <i class="material-icons">settings</i>
            </button>

            <!--<button class="cloud">
                <i class="material-icons">cloud</i>
            </button>-->
        </div>
        <!-- Navigationsleiste "mitte" -->
        <nav>
            <button class="ng">
                <i class="material-icons">&#xE8D3;</i>
                <span>New Group</span>
            </button>

            <button class="nc">
                <i class="material-icons">&#xE0B6;</i>
                <span>New Channel</span>
            </button>

            <button class="cn">
                <i class="material-icons">&#xE851;</i>
                <span>Contacts</span>
            </button>

            <!--<button class="cl">
                <i class="material-icons">&#xE0B0;</i>

                <span>Calls History</span>
            </button>-->

            <!--<a href="https://telegram.org/faq" target="_blank">
                <button class="faq">
                    <i class="material-icons">&#xE000;</i>

                    <span>FAQ and Support</span>
                </button>
            </a>-->

            <button class="lo" onclick="logout()">
                <i class="material-icons">&#xE879;</i>
                <span>Logout</span>
            </button>
        </nav>

        <div class="info">
            <p>TextMe - Pineapple</p>
            <p>Version 0.0.0 <!--- <a href="https://en.wikipedia.org/wiki/Telegram_(messaging_service)">About</a>--></p>
            <!--<p>Layout coded by: <a href="https://www.facebook.com/mayrinckdesign">Mayrinck</a></p>-->
        </div>
    </div>
</section>

<!-- MOBILE OVERLAY -->
<section class="switchMobile">
    <!-- hier ist noch ein Fehler, wenn ich vorher moreMenu geöffnet habe bleibt es eingeblendet -->
    <p class="title">Mobiles Gerät entdeckt</p>

    <p class="desc">Wechseln Sie zu der Mobilen Seite</p>

    <a href=""><button class="okay">OK</button></a>
</section>

<!-- PROFILE OPTIONS OVERLAY -->
<section class="config">
    <section class="configSect">
        <div class="profile">
            <p class="confTitle">Settings</p>

            <div class="image"></div>

            <div class="side">
                <p class="name" id="nutzernamen2">Random Name</p>
                <p id="email2">email Adresse?</p>
                <!--<p class="pStatus">Online</p>-->
            </div>

            <button class="changePic">Change Profile Picture</button>
            <button class="edit">Edit Profile Info</button>
        </div>
    </section>

    <section class="configSect second">

        <!-- PROFILE INFO SECTION -->
        <!--<p class="confTitle">Your Info</p>

        <div class="information">
            <ul>
                <li>eMail-Adresse: <span class="blue phone">+1 12 1234 5678</span></li>
                <li>Username: <span class="blue username">USERNAME</span></li>
                <li>Profile: <span class="blue">https://t.me/USERNAME</span></li>
            </ul>
        </div>-->

        <!-- NOTIFICATIONS SECTION -->
        <p class="confTitle">Notifications</p>

        <div class="optionWrapper deskNotif">
            <input type="checkbox" id="deskNotif" class="toggleTracer" checked>

            <label class="check deskNotif" for="deskNotif">
                <div class="tracer"></div>
            </label>
            <p>Enable Desktop Notifications</p>
        </div>

        <div class="optionWrapper showSName">
            <input type="checkbox" id="showSName" class="toggleTracer">

            <label class="check" for="showSName">
                <div class="tracer"></div>
            </label>
            <p>Show Sender Name</p>
        </div>

        <div class="optionWrapper showPreview">
            <input type="checkbox" id="showPreview" class="toggleTracer">

            <label class="check" for="showPreview">
                <div class="tracer"></div>
            </label>
            <p>Show Message Preview</p>
        </div>

        <div class="optionWrapper playSounds">
            <input type="checkbox" id="playSounds" class="toggleTracer">

            <label class="check" for="playSounds">
                <div class="tracer"></div>
            </label>
            <p>Play Sounds</p>
        </div>


        <p class="confTitle">Other Settings</p>

        <div class="optionWrapper">
            <input type="checkbox" id="checkNight" class="toggleTracer">

            <label class="check" for="checkNight">
                <div class="tracer"></div>
            </label>
            <p>Night Mode</p>
        </div>

    </section>
</section>

<!-- DARK FRAME OVERLAY -->
<section class="overlay"></section>

<!-- -------------------------------- -->
<!-- SPECIFIC FOR CONNECTION WARNINGS -->
<!-- -------------------------------- -->
<div class="alerts">
    Trying to reconnect...
</div>

<!-- CONVERSATION OPTIONS MENU -->
<!-- drei Punkte oben rechts -->
<div class="moreMenu">
    <button class="option about">Irgendwas</button>
    <!--<button class="option about">See Info</button>
    <button class="option notify">Disable Notifications</button>
    <button class="option block">Block User</button>-->
</div>

<?php
$username = $Database->getCurrentUser();
$userid = $Database->getUserID($username);
$userid = $userid[0][0];
$email = $Database->getEmail($userid);
$email = $email[0][0];

echo "<script>
    document.getElementById('nutzernamen').innerHTML = '$username';
    document.getElementById('nutzernamen2').innerHTML = '$username';
    document.getElementById('email').innerHTML='$email';
    document.getElementById('email2').innerHTML='$email';
</script>";
?>

<!-- JS einbinden -->
<script src='../js/vendor/jquery.min.js'></script>
<script  src="../js/mainpage.js"></script>
</body>
</html>
