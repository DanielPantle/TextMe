<?php
//db.php einbinden
include("./../php/db.php");

//neue Klasse Database erstellen
$Database = new Database();

// prüfen, ob User eingeloggt ist
if(!$Database->isLoggedIn()) {
    // zur Login-Seite weiterleiten - falls nicht eingeloggt
    header('Location: ./../');
}

// prüft, ob User Admin ist
if(!$Database->isUserAdmin()){
    // zur Login-Seite weiterleiten - falls User nicht admin
    header('Location: ./../');
}

//TODO: Text auf Deutsch / Englisch ändern
//Schreibe in Variabeln die Angaben für die Statistik
//Alle Informationen werden mithilfe der von PHP Funktionen aus der Datenbank erzeugt
$oneHour = '<div class="form-group" id="statisticContent">Gesendete Nachrichten in der letzten Stunde: ' . $Database->msgCountPerTime(1).'</div>';
$twentyfourHour = '<div class="form-group" id="statisticContent">Gesendete Nachrichten in den letzten 24 Stunden: ' . $Database->msgCountPerTime(24).'</div>';
$registeredUsers = '<div class="form-group" id="statisticContent">Registrierte Benutzer: ' . $Database->userCount().'</div>';
$totalMessages = '<div class="form-group" id="statisticContent">Nachrichten insgesamt versendet: ' . $Database->totalChatMessages().'</div>';
$recentlyActive = '<div class="form-group" id="statisticContent">Aktive Nutzer in den letzten 5 Minuten: ' . $Database->recentlyActive().'</div>';
$onlineUsers = '<div class="form-group" id="statisticContent">Nutzer online: ' . $Database->onlineUsers().'</div>';

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!--Browser / Tab - Title einstellen-->
        <title>TextMe - Statistik</title>

        <!--Meta Informationen der Website-->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!--Stylesheets einbinden-->
        <link rel="stylesheet" href="./../css/statistic.css">
        <link rel="stylesheet" href="./../css/mainpage.css">

        <!--Icon "einrichten"-->
        <link rel="icon" href="./../images/Speach-BubbleDialog-512.png">
    </head>
<body>
    <!-- noscript Funktion -
    Fängt User ab die JavaScript deaktivert haben
    weißt auf eine Anleitung hin um JavaScript wieder zu aktivieren
    -->
    <noscript>
        <!-- TODO: Text auf Deutsch / Englisch ändern-->
        <div id="nojavascript">
            Diese Anwendung benötitgt JavaScript zum ordungsgemäßen Betrieb.
            Bitte <a href="https://www.enable-javascript.com/" target="_blank" rel="noreferrer"> aktivieren Sie Java Script</a>
            und laden Sie die Seite neu.
        </div>
    </noscript>

    <div class="modal-content" style="top:0;">
        <!-- Überschrift -->
        <div class="modal-header">
            <h2>Statistik</h2>
        </div>

        <!-- "Body" - Informationen -->
        <div class="modal-body">
            <?php
                //alle vorher Definierten Variabeln werden aufgerufen
                echo $oneHour;
                echo $twentyfourHour;
                echo $registeredUsers;
                echo $totalMessages;
                echo $recentlyActive;
                echo $onlineUsers;
            ?>
        </div>
        <!-- TODO: Text auf Deutsch / Englisch ändern -->
        <!-- Fuß der Seite -->
        <!-- Link zum ausloggen / Link zurpck zum Chat -->
        <div class="modal-footer">
            <h4><a id="toChat" href="./index.php">zum Chat</a> // <span style = "display: inline;"><a id="toLogout" href="./../logout.php">Logout</a></span></h4>
        </div>
    </div>

    <!-- Java Script Dateien einbinden -->
    <script src='./../js/vendor/jquery.min.js'></script>
    <script  src='./../js/statistic.js'></script>
</body>
</html>
