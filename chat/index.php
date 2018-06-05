<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
include("./../php/db.php");
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
    header('Location: ./../');
}
//print_r($chatverlauf);

if(isset($_POST['editSubmitButton'])) {

    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];

    $username = $Database->getCurrentUser();
    $userid = $Database->getUserID();
    $email = $Database->getEmail($userid);

    if($Database->login($username, $currentPassword)) {
        if($newPassword != "" && $Database->changePasswordByEmail($email,$newPassword)) {
            echo "<div class='alert alert-success'>Password changed successfully!</div>";
        } else {
            echo "failure!";
        }
    } else {
        echo "<div class='alert alert-danger'>False current password! Please try again...</div>";
    }

}


// Link überprüfen
if(isset($_SESSION['link'])) {
    $link = $_SESSION['link'];
    $uiic = $Database->getLinkData($link);

    if($uiic) {
        $uid = $uiic['uid'];
        $cid = $uiic['cid'];
        $chatName = $Database->getChatnameById($cid);

        $res = $Database->joinChat($cid);
        $invitor = $Database->getUserNameById($uid);

        if($res && $res > 0) {
            echo "<script>var linkResult = 'Du wurdest erfolgreich dem Chat $chatName hinzugefügt. (Eingeladen von $invitor)';</script>";
            $username_link = $Database->getCurrentUser();
            $message_link =" wurde dem Chat hinzugefuegt";
            $Database->writeMessage($cid,$message_link);
        }
        else {
            echo "<script>var linkResult = 'Du bist schon in dem Chat $chatName. (Eingeladen von $invitor)';</script>";
        }
    }

    unset($_SESSION['link']);
}


?>
    <meta charset="UTF-8">
    <title>TextMe - Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="./../css/Icons.css" rel="stylesheet">
    <link rel="stylesheet" href="./../css/mainpage.css">
    <link rel="icon" href="./../images/Speach-BubbleDialog-512.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">

    <!-- Emoji-Font -->
    <link href="https://afeld.github.io/emoji-css/emoji.css" rel="stylesheet">
</head>
<body>
<!-- -------- -->
<!-- MAIN APP -->
<!-- -------- -->
<div id="messageContainer" class='alert alert-success' style="display: none"></div>

<noscript>
    <div id="nojavascript">
        Diese Anwendung benötitgt JavaScript zum ordungsgemäßen Betrieb.
        Bitte <a href="https://www.enable-javascript.com/" target="_blank" rel="noreferrer"> aktivieren Sie Java Script</a>
        und laden Sie die Seite neu.
    </div>
</noscript>
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
        <div class="chats" id="chats">
        </div>
    </div>
    <!-- Rechte Seite der Seite in JS -->
    <div class="rightPanel">
        <!-- obere Leiste des Chats -->
        <div class="topBar">
            <div class="rightSide">
                <button id="chatOption">
                        <i class="material-icons">more_vert</i>
                </button>
            </div>

            <div class="leftSide">
                <p class="chatName" id="chatname"></p>
                <p class="chatStatus" id="mitglieder"></p>
            </div>
        </div>
        <!-- Chat verlauf -->
        <div class="convHistory userBg" id="chatVerlauf">
            <!-- CONVERSATION GOES HERE! -->
        </div>
        <!-- Leiste unter dem Chat -->
        <div class="replyBar">

            <!-- Eingabefeld -->
            <div id="inputChatMessage" contenteditable="true" onkeypress="return onEnter(event)" class="replyMessage"></div>

            <div class="emojiBar">
                <!-- Emoji's /Stickers / GIF -->
                <div class="emoticonType">
                    <button id="panelEmoji">Emojis</button>
                </div>

                <!-- Emoji panel -->
                <div class="emojiList">
                    <button id="smileface" class="pick"><i class="em-svg em-slightly_smiling_face"></i></button>
                    <button id="tearjoyface" class="pick"><i class="em-svg em-joy"></i></button>
                    <button id="laughingface" class="pick"><i class="em-svg em-smiley"></i></button>
                    <button id="sadface" class="pick"><i class="em-svg em-slightly_frowning_face"></i></button>
                    <button id="cryingface" class="pick"><i class="em-svg em-sob"></i></button>
                    <button id="surpriseface" class="pick"><i class="em-svg em-open_mouth"></i></button>
                    <button id="winkface" class="pick"><i class="em-svg em-smirk"></i></button>
                    <button id="cheekyface" class="pick"><i class="em-svg em-stuck_out_tongue"></i></button>

                    <!--<button id="rofl" class="pick"></button>-->
                    <!--<button id="somface" class="pick"></button>-->
                    <!--<button id="swfface" class="pick"></button>-->

                    <!-- https://afeld.github.io/emoji-css/ -->
                </div>
            </div>

            <div class="otherTools">
                <button class="toolButtons emoji">
                    <i class="material-icons">face</i>
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
            <!-- Platz für User Profilbild -->
            <div id="imageplace"></div>

            <!-- User Info (Name / Email Adresse) -->
            <div class="myinfo">
                <p class="name" id="nutzernamen">Random Name</p>
                <p id="email"> eMail Adresse?</p>
            </div>

            <button class="settings">
                <i class="material-icons">settings</i>
            </button>

        </div>
        <!-- Navigationsleiste "mitte" -->
        <nav>
            <button class="nc">
                <i class="material-icons">&#xE8D3;</i>
                <span>Neuen Chat erstellen</span>
            </button>
            <div class="nc-div" style="display: none">
                <input id="nc-chatnameField" type="text" placeholder="chatname">
                <div class="row">
                    <button class="col" id="nc-abort" type="button">abort</button>
                    <button class="col" id="nc-create" type="button">create</button>
                </div>
            </div>
            <button class="ng">
                <i class="material-icons">&#xE0B6;</i>
                <span>Random-Chat erstellen</span>
            </button>

            <!-- Button / icon Statistik - ausgeblendet -->
            <button class="cn" style="display: none">
                <i class="fa fa-bar-chart-o" style="font-size:20px;color:#999"></i>
                <!-- TODO: Text auf Deutsch / Englisch ändern -->
                <span>Statistik</span>
            </button>

            <button class="lo">
                <i class="material-icons">&#xE879;</i>
                <span>Logout</span>
            </button>
        </nav>

        <div class="info">
            <p>TextMe - Pineapple</p>
            <p>Version 0.0.0</p>
        </div>
    </div>
</section>

<!-- MOBILE OVERLAY 
<section class="switchMobile">
    hier ist noch ein Fehler, wenn ich vorher moreMenu geöffnet habe bleibt es eingeblendet
    <p class="title">Mobiles Gerät entdeckt</p>

    <p class="desc">Wechseln Sie zu der Mobilen Seite</p>

    <a href=""><button class="okay">OK</button></a>
</section> -->

<!-- PROFILE OPTIONS OVERLAY -->
<section class="config">
    <section class="configSect">
        <div class="profile">
            <p class="confTitle">Einstellungen</p>
            <div id="imageplace2"></div>
            <div class="side">
                <p class="name" id="nutzernamen2">Random Name</p>
                <p id="email2">email Adresse?</p>
            </div>

            <button class="edit">Edit Profile Info</button>
        </div>
    </section>

    <section class="configSect second">
        <p class="confTitle">Profilbild</p>

        <form method="post" enctype="multipart/form-data">
            <br>
            <input class="choosePicture"  type="file" name="image">
            <br> <br>
            <input class="changePicture" type="submit" name="submit-create" value="Speichern">
            <input class="changePicture" type="submit" name="submit-delete" value="Löschen">
        </form>
        <br>
    </section>
    <section class="configSect2" style="display: none">
        <div class="editPersonalInfoDiv">
            <p class="confTitle">Change Password</p>

            <form method="post" class="editPersonalInfoForm">
                <!--
                <label for="emailField">E-Mail</label>
                <input type="email" id="emailChangeField">
                -->
                <input class="form-control" type="password" name="currentPassword" placeholder="Current Password" id="passwordCurrentField">
                <div class="alert alert-danger" role="alert" style=" display:none;" id="current-password-error">Current Password must not be empty!</div>
                <br>
                <input class="form-control" type="password" name="newPassword" placeholder="New Password" id="passwordChangeField">
                <div class="alert alert-danger" role="alert" style=" display:none;" id="password-error">minimum six characters!</div>
                <br>
                <input class="form-control" type="password" placeholder="Confirm Password" id="passwordConfirmChangeField">
                <div class="alert alert-danger" role="alert" style=" display:none;" id="password-confirm-error">passwords doesn't match!</div>
                <br> <br>
                <input class="editSubmitButton" name="editSubmitButton" type="submit"  value="Speichern">
            </form>
        </div>
    </section>


    <?php
    if(isset($_POST['submit-create'])){
        if(getimagesize($_FILES['image']['tmp_name'])==false){
            echo "Please select an image";
        }else {
            $image = addslashes($_FILES['image']['tmp_name']);
            $name = addslashes($_FILES['image']['name']);
            $image = file_get_contents($image);
            $image= base64_encode($image);
            $result ="";
            if($Database->haveUserPicture()){
                $result = $Database->updatePictureFromCurrentUser($name,$image);
                // TODO:  hier seite neu laden --> ajax -> dodo
                //echo "user hat ein bild";
                //echo $result;
            }else {
                $result = $Database->createPictureFromUser($name,$image);
                // TODO:  hier seite neu laden --> ajax -> dodo
                //echo "user hat kein bild";
                //echo $result;
            }
        }
    }
    if(isset($_POST['submit-delete'])){
        if($Database->haveUserPicture()){
            $result = $Database->deletePicture();
            // TODO:  hier seite neu laden --> ajax -> dodo
        }
    }

    /*$result = $Database->showPictureFromCurrentUser();
    //print_r($result);
    $count = count($result);
    if($count>0){
        $bild= $result[0]['imgdata'];
        echo '<img  src="data:image;base64,'.$bild.'">';
    }else echo '<img src="./../images/Profilbild_default.jpg">'*/
    ?>



        <!-- NOTIFICATIONS SECTION -->


    <!--<section class="configSect second">
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
    </section>-->
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
    <button class="option about" id="delete_chat">Chat löschen</button>
    <button class="option about" id="send_invitation">Einladung versenden</button>
</div>

<!-- JS einbinden -->
<script src='./../js/vendor/jquery.min.js'></script>
<script  src="./../js/mainpage.js"></script>
</body>
</html>
