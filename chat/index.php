<?php
//db.php einfügen
include("./../php/db.php");

//neue Klasse Database einbinden
$Database = new Database();

// prüfen, ob User eingeloggt ist
if(!$Database->isLoggedIn()) {
    // zur Login-Seite weiterleiten - falls nicht eingeloggt
    header('Location: ./../');
}

//Password ändern - sobald editSubmitButton gedrückt wurde
if(isset($_POST['editSubmitButton'])) {

    //übergebene Daten (per Post) in Variabeln abspeichern
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];

    //username,userid und email aus der Datenbank über die Datenbank Klasse in die entsprechenden Variabeln abspeichern
    $username = $Database->getCurrentUser();
    $userid = $Database->getUserID();
    $email = $Database->getEmail($userid);

    //Überprüft Usernamen und aktuelles Password durch einen login Vorgang
    if($Database->login($username, $currentPassword)) {
        //Ändert Password anhand der Email Adresse und wenn das neue Password nicht leer ist
        if($newPassword != "" && $Database->changePasswordByEmail($email,$newPassword)) {
            //Ausgabe - Positive Rückmeldung
            //TODO: Text auf Deutsch / Englisch ändern
            echo "<div class='alert alert-success'>Password changed successfully!</div>";
        } else {
            echo "failure!";
        }
    } else {
        //Ausgabe - Negative Rückmeldung
        //TODO: Text auf Deutsch / Englisch ändern
        echo "<div class='alert alert-danger'>False current password! Please try again...</div>";
    }
}

// Profilbild ändern sobald button gedrückt wurde
if(isset($_POST['submit-create'])){
        //Überprüft ob ein Bild im "ausgewählt" wurde
        if(getimagesize($_FILES['image']['tmp_name'])==false){
            //TODO: Text auf Deutsch / Englisch ändern
            echo "Please select an image";
        }else {
            //wenn ja speichert er die Daten in Variabeln ab
            $image = addslashes($_FILES['image']['tmp_name']);
            $name = addslashes($_FILES['image']['name']);
            $image = file_get_contents($image);
            $image= base64_encode($image);
            $result ="";
            //wenn der User ein Profilbild hat
            if($Database->haveUserPicture()){
                //Update falls bereits ein Profilbild vorhanden ist
                $result = $Database->updatePictureFromCurrentUser($name,$image);
                //lädt die Seite neu sobald das Bild geupdatet wurde
                echo "<script language=\"javascript\">document.location.reload;</script>";
            }else {
                //Erstellt ein Profilbild falls noch keins vorhanden ist
                $result = $Database->createPictureFromUser($name,$image);
                //lädt die Seite neu sobald das erstellt wurde
                echo "<script language=\"javascript\">document.location.reload;</script>";
            }
        }
    }

//Profilbild löschen sobald button gedrückt wurde
if(isset($_POST['submit-delete'])){
        //Überprüft ob User ein Profilbild hat
        if($Database->haveUserPicture()){
            //löscht Profilbild fallseins vorhanden ist
            $result = $Database->deletePicture();
            //lädt die Seite neu sobald das erstellt wurde
            echo "<script language=\"javascript\">document.location.reload;</script>";
        }
    }

// Link überprüfen
if(isset($_SESSION['link'])) {
    $link = $_SESSION['link'];
    $uiic = $Database->getLinkData($link);
    if($uiic) {
        //user ID und Chat ID aus Variabel uiic laden
        $uid = $uiic['uid'];
        $cid = $uiic['cid'];
        //Chatname in Variabel abspeichern anhand der ChatID
        $chatName = $Database->getChatnameById($cid);
        //current User in Chat hinzufügen anhand der ChatID
        $res = $Database->joinChat($cid);
        //holt den Namen des einladendes anhand der UserID
        $invitor = $Database->getUserNameById($uid);
        if($res && $res > 0) {
            //Ausgabe - Postiv - User wurde dem Chat hinzugefügt
            //TODO: Text auf Deutsch / Englisch ändern
            echo "<div class='alert alert-success'>Du wurdest erfolgreich dem Chat $chatName hinzugefügt. (Eingeladen von $invitor)</div>";
            //Message senden - sodass User Informiert werden das jemand dem Chat hinzugefügt wurde
            $message_link =" wurde dem Chat hinzugefuegt";
            $Database->writeMessage($cid,$message_link);
        }
        else {
            //Ausgabe - Negativ - falls User bereis im Chat ist
            //TODO: Text auf Deutsch / Englisch ändern
            echo "<div class='alert alert-danger'>Du bist schon in dem Chat $chatName. (Eingeladen von $invitor)</div>";
        }
    }
    //zurück setzen des der Session Variabel 'link'
    unset($_SESSION['link']);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!--Browser / Tab - Title einstellen-->
        <title>TextMe - Chat</title>

        <!--Meta Informationen der Website-->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

        <!--Stylesheets einbinden-->
        <link rel="stylesheet" href="./../css/Icons.css">
        <link rel="stylesheet" href="./../css/mainpage.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://afeld.github.io/emoji-css/emoji.css">

        <!--Icon "einrichten"-->
        <link rel="icon" href="./../images/Speach-BubbleDialog-512.png">
    </head>
    <body>
        <!-- Nachrichten Kontainer - ausgeblendet -->
        <div id="messageContainer" class='alert alert-success' style="display: none"></div>

        <!-- noscript Funktion -
        Fängt User ab die JavaScript deaktivert haben
        weißt auf eine Anleitung hin um JavaScript wieder zu aktivieren
        -->
        <noscript>
            <div id="nojavascript">
                <!-- TODO: Text auf Deutsch / Englisch ändern-->
                Diese Anwendung benötitgt JavaScript zum ordungsgemäßen Betrieb.
                Bitte <a href="https://www.enable-javascript.com/" target="_blank" rel="noreferrer"> aktivieren Sie Java Script</a>
                und laden Sie die Seite neu.
            </div>
        </noscript>

        <!-- Hauptanwendung -->
        <section class="mainApp">
            <!-- Linker Bereich der Anwendung -->
            <div class="leftPanel">
                <!-- Menü und Suche -->
                <header>
                    <!-- Menü Button -->
                    <button class="trigger">
                        <i class="material-icons">&#xE5D2;</i>
                    </button>
                    <!-- Chat Suche -->
                    <input class="searchChats" type="search" placeholder="Search..."/>
                </header>
                <!-- Chat auflistung -->
                <div class="chats" id="chats"></div>
            </div>

            <!-- Rechte Seite der Anwendung -->
            <div class="rightPanel">
                <!-- obere Leiste -->
                <div class="topBar">
                    <div class="rightSide">
                        <!-- Chat Optionen -->
                        <button id="chatOption">
                                <i class="material-icons">more_vert</i>
                        </button>
                    </div>
                    <!-- Chat Info (Chatname / Mitglieder) -->
                    <div class="leftSide">
                        <p class="chatName" id="chatname"></p>
                        <p class="chatStatus" id="mitglieder"></p>
                    </div>
                </div>

                <!-- Chat verlauf -->
                <div class="convHistory userBg" id="chatVerlauf"></div>

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

        <!-- Overlay  Bereich -->
        <!-- MENU -->
        <section class="menuWrap">
            <div class="menu">
                <!-- menu "oben" - User Info -->
                <div class="me userBg">
                    <!-- Platz für User Profilbild -->
                    <div id="imageplace"></div>

                    <!-- User Info (Name / Email Adresse) -->
                    <div class="myinfo">
                        <p class="name" id="nutzernamen"></p>
                        <p id="email"></p>
                    </div>
                    <!-- Einstellungsbutton -->
                    <button class="settings">
                        <i class="material-icons">settings</i>
                    </button>
                </div>

                <!-- Navigationsleiste -->
                <nav>
                    <!-- Button / Icon Chat erstellen -->
                    <button class="nc">
                        <i class="material-icons">&#xE8D3;</i>
                        <!-- TODO: Text auf Deutsch / Englisch ändern -->
                        <span>Neuen Chat erstellen</span>
                    </button>

                    <!-- Chat erstellen -->
                    <div class="nc-div" style="display: none">
                        <!-- Eingabefeld Chatname -->
                        <input id="nc-chatnameField" type="text" placeholder="chatname">
                        <!-- Button chat erstellen / chat erstellen abbrechen -->
                        <div class="row">
                            <!-- TODO: Text auf Deutsch / Englisch ändern -->
                            <button class="col" id="nc-abort" type="button">abort</button>
                            <button class="col" id="nc-create" type="button">create</button>
                        </div>
                    </div>

                    <!-- Button / icon Random Chat erstellen -->
                    <button class="ng">
                        <i class="material-icons">&#xE0B6;</i>
                        <!-- TODO: Text auf Deutsch / Englisch ändern -->
                        <span>Random-Chat erstellen</span>
                    </button>

                    <!-- Button / icon Statistik - ausgeblendet -->
                    <button class="cn">
                        <i class="fa fa-bar-chart-o" style="font-size:20px;color:#999"></i>
                        <!-- TODO: Text auf Deutsch / Englisch ändern -->
                        <span>Statistik</span>
                    </button>

                    <!--Button / icon Logout -->
                    <button class="lo">
                        <i class="material-icons">&#xE879;</i>
                        <!-- TODO: Text auf Deutsch / Englisch ändern -->
                        <span>Logout</span>
                    </button>
                </nav>

                <!-- Application Info -->
                <div class="info">
                    <p>TextMe - Pineapple</p>
                    <!-- TODO: Text auf Deutsch / Englisch ändern -->
                    <p>Version 0.0.0</p>
                </div>
            </div>
        </section>

        <!-- Overlay Bereich --->
        <!-- Einstellungen -->
        <section class="config">
            <section class="configSect">
                <!-- obere Bereich von der Einstellungen -->
                <div class="profile">
                    <!-- TODO: Text auf Deutsch / Englisch ändern-->
                    <p class="confTitle">Einstellungen</p>
                    <!-- Platzhalter für das Profilbild -->
                    <div id="imageplace2"></div>

                    <!-- Nutzernamen und Email adresse anzeigen-->
                    <div class="side">
                        <p class="name" id="nutzernamen2"></p>
                        <p id="email2"></p>
                    </div>

                    <!-- TODO: Text auf Deutsch / Englisch ändern-->
                    <!-- Button edit Profil Password -->
                    <button class="edit">Edit Profile Password</button>
                </div>
            </section>

            <!-- Bereich zur Profilbid änderung -->
            <section class="configSect second">
                <!-- Bereich Title -->
                <p class="confTitle">Profilbild</p>

                <form method="post" enctype="multipart/form-data">
                    <br>
                    <!-- Imput bereich für neue Bilder -->
                    <input class="choosePicture"  type="file" name="image">
                    <br> <br>
                    <!-- neues Profilbild speichern / Aktuelles Profilbild Löschen Button -->
                    <!-- TODO: Text auf Deutsch / Englisch ändern-->
                    <input class="changePicture" type="submit" name="submit-create" value="Speichern">
                    <!-- TODO: Text auf Deutsch / Englisch ändern-->
                    <input class="changePicture" type="submit" name="submit-delete" value="Löschen">
                </form>
                <br>
            </section>

            <!-- Bereich zum Password ändern -->
            <section class="configSect2" style="display: none">
                <div class="editPersonalInfoDiv">
                    <!-- TODO: Text auf Deutsch / Englisch ändern-->
                    <!-- Bereich Title -->
                    <p class="confTitle">Change Password</p>

                    <!-- TODO: Text auf Deutsch / Englisch ändern-->
                    <form method="post" class="editPersonalInfoForm">
                        <!-- Imput Feld für aktuelles Password -->
                        <input class="form-control" type="password" name="currentPassword" placeholder="Current Password" id="passwordCurrentField">
                        <!-- Error ausgabe falls kein password eingegeben wurde -->
                        <div class="alertPassword alert-danger" role="alert" style=" display:none;" id="current-password-error">Current Password must not be empty!</div>
                        <br>
                        <!-- Imput Feld für neues Password -->
                        <input class="form-control" type="password" name="newPassword" placeholder="New Password" id="passwordChangeField">
                        <!-- Error ausgabe falls zuwenig Zeichen eingegeben wurde -->
                        <div class="alertPassword alert-danger" role="alert" style=" display:none;" id="password-error">minimum six characters!</div>
                        <br>
                        <!-- Imput Feld zur Überprüfung des neuen Passwords -->
                        <input class="form-control" type="password" placeholder="Confirm Password" id="passwordConfirmChangeField">
                        <!-- Error ausgabe falls die beiden Passwörter nicht überein stimmen -->
                        <div class="alertPassword alert-danger" role="alert" style=" display:none;" id="password-confirm-error">passwords doesn't match!</div>
                        <br> <br>
                        <!-- Submit Button zur Speicherung des neuen Passwords -->
                        <input class="editSubmitButton" name="editSubmitButton" type="submit"  value="Speichern">
                    </form>
                </div>
            </section>
        </section>

        <!-- Overlay "Bereich"
        sodass man alles schließen kann außerhalb des Menüs und der Einstellungen -->
        <section class="overlay"></section>

        <!-- Chat Menü (drei Punkte oben rechts) -->
        <div class="moreMenu">
            <!-- TODO: Text auf Deutsch / Englisch ändern-->
            <!-- Buton zum Chat löschen -->
            <button class="option about" id="delete_chat">Chat löschen</button>
            <!-- Buton zum Einladungslink verschicken -->
            <button class="option about" id="send_invitation">Einladung versenden</button>
        </div>

        <!-- Java Script Dateien einbinden -->
        <script src='./../js/vendor/jquery.min.js'></script>
        <script  src="./../js/anchorme.min.js"></script>
        <script  src="./../js/mainpage.js"></script>
    </body>
</html>