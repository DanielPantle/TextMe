<?php
include("./../php/db.php");
$Database = new Database();
if(!$Database->isLoggedIn()) {
    // zur Login-Seite weiterleiten
    header('Location: ./../');
}
if(!$Database->isUserAdmin()){
    // zur Login-Seite weiterleiten
    header('Location: ./../');
}

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
    <meta charset="UTF-8">
    <title>TextMe - Statistik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./../css/statistic.css" rel="stylesheet">
    <link rel="icon" href="./../images/Speach-BubbleDialog-512.png">
    <link rel="stylesheet" href="./../css/mainpage.css">
</head>
<body>
    <noscript>
        <div id="nojavascript">
            Diese Anwendung benötitgt JavaScript zum ordungsgemäßen Betrieb.
            Bitte <a href="https://www.enable-javascript.com/" target="_blank" rel="noreferrer"> aktivieren Sie Java Script</a>
            und laden Sie die Seite neu.
        </div>
    </noscript>

    <div class="modal-content" style="top:0;">
        <div class="modal-header">
            <h2>Statistik</h2>
        </div>
        <div class="modal-body">
            <?php
                echo $oneHour;
                echo $twentyfourHour;
                echo $registeredUsers;
                echo $totalMessages;
                echo $recentlyActive;
                echo $onlineUsers;
            ?>
        </div>
        <div class="modal-footer">
            <h4><a id="toChat" href="./index.php">zum Chat</a> // <span style = "display: inline;"><a id="toLogout" href="./../logout.php">Logout</a></span></h4>
        </div>
    </div>
    <script src='./../js/vendor/jquery.min.js'></script>
    <script  src="./../js/statistic.js"></script>
</body>
</html>
