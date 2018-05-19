<!DOCTYPE html>
<html lang="en">
<head>
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

?>
    <meta charset="UTF-8">
    <title>TextMe - Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="./../css/Icons.css" rel="stylesheet">
    <link rel="stylesheet" href="./../css/mainpage.css">
    <link rel="icon" href="./../images/Speach-BubbleDialog-512.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<body>
<!-- -------- -->
<!-- MAIN APP -->
<!-- -------- -->

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
        <div class="chats">
            <?php
                $chats = $Database->getAllChatsFromCurrentUser();
                //print_r($chats);
                $count = count($chats);
                //echo $count;
                if ($count > 0){
                    echo "<br>";
                    for ($j=0;$j<$count;$j++){
                        $deleted[$j] = $Database->isChatDeletedForUser($chats[$j]['cid']);
                        //print_r($members_2[$j]);
                        //$deleted[$j]=1;
                        if (!$deleted[$j]){
                            $cid[$j]=$chats[$j]['cid'];
                            $chatname[$j] =$chats[$j]['chatname'];
                            $members[$j] = $Database->getMembersOfChat($chats[$j]['cid']);
                            $count_2 = count($members[$j]);
                            $members_2[$j]="";
                            if($count_2>1){
                                $members_10=$members[$j][0]['name'];
                                $members_20=$members[$j][1]['name'];
                            }else {
                                $members_10=$members[$j][0]['name'];
                                $members_20="NV";
                            }
                            for ($i=0;$i<$count_2;$i++){
                                if($members_2[$j]==""){
                                    $members_2[$j] = $members[$j][$i]['name'];
                                }else $members_2[$j]=$members_2[$j]." , ".$members[$j][$i]['name'];
                            }
                            /*$members[$j] =$chats[$j]['members'];
                            $members[$j] = explode(',',$members[$j]);
                            $members_0 = $members[$j];
                            $count_2 = count($members[$j]);
                            $members_2="";
                            for ($i=0;$i<$count_2;$i++){
                                if($members_2==""){
                                    $members_2 = $members[$j][$i];
                                }else $members_2 =$members_2." , ".$members[$j][$i];

                            }*/
                            //$members[$j] = $Database->getMembersOfChat($cid[$j]);
                            $history = conHistory($cid[$j],$Database,$count_2);
                            if($count_2>2){
                                echo "<div class='chatButton' onclick='chatButtonClick($cid[$j],\"$chatname[$j]\",\"$members_2[$j]\",\"$history\");'> 
                                <div class='chatInfo'>
                                    <div class='image'>
                                        
                                    </div>
                                    <p class='name'>
                                        $chatname[$j]                        
                                    </p>
                                    <p class='message'>
                                        Mitglieder: $members_2[$j]
                                    </p>
                                </div>
                              </div>
                            ";
                            }else {
                                if($members_20=="NV"){
                                    $members_0="--> kein Chat Partner vorhanden <--";
                                }else {
                                    $username = $Database->getCurrentUser();
                                    if($members_10==$username){
                                        $members_0 = "An: ".$members_20;
                                    }else {
                                        $members_0 = "An: ".$members_10;
                                    }
                                }
                                echo "<div class='chatButton' onclick='chatButtonClick($cid[$j],\"$chatname[$j]\",\"$members_2[$j]\",\"$history\");'> 
                                <div class='chatInfo'>
                                    <div class='image'>
                                        
                                    </div>
                                    <p class='name'>
                                        $chatname[$j]                        
                                    </p>
                                    <p class='message'>
                                        $members_0
                                    </p>
                                </div>
                              </div>
                            ";
                            }
                        }
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
            <?php
                function conHistory($chatid,$Database,$count_2){
                    $return = "";
                    foreach ($Database->getAllMessagesFromChat($chatid) as $date => $messages) {
                        $return = $return."<div class=\\\"chatDatum\\\">$date</div>";
                        foreach ($messages as $message) {
                            $nachricht = $message['message'];
                            $zeit = $message['time'];
                            $name = $message['name'];
                            $username = $Database->getCurrentUser();
                            if($nachricht==" hat den Chat verlassen"){
                                $return =$return."<div class=\\\"chatVerlassen\\\"><b>$name</b>$nachricht</div>";
                            }else {
                                if($username==$name){
                                    $return = $return."<div class=\\\"msg messageSent\\\">$nachricht<span class=\\\"timestamp\\\">$zeit</span></div>";
                                }
                                else if($count_2>2){
                                    $return = $return."<div class=\\\"msg messageReceived\\\">$name: $nachricht<span class=\\\"timestamp\\\">$zeit</span></div>";
                                }else {
                                    $return = $return."<div class=\\\"msg messageReceived\\\">$nachricht<span class=\\\"timestamp\\\">$zeit</span></div>";
                                }
                            }
                        }
                    }
                    return $return;
                }
            ?>
        </div>
        <!-- Leiste unter dem Chat -->
        <div class="replyBar">

            <!-- Eingabefeld -->
            <input id="inputChatMessage" type="text" onkeypress="return onEnter(event)" class="replyMessage" placeholder="Type your message..."/>

            <div class="emojiBar">
                <!-- Emoji's /Stickers / GIF -->
                <div class="emoticonType">
                    <button id="panelEmoji">Emojis</button>
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
            <div class="image"></div>

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
            <button class="ng">
                <i class="material-icons">&#xE8D3;</i>
                <span>Neue Gruppe</span>
            </button>

            <button class="nc">
                <i class="material-icons">&#xE0B6;</i>
                <span>Neuer Chat </span>
            </button>
            <?php
                if($Database->isUserAdmin()){
                    echo "<button class=\"cn\">
                                <i class=\"fa fa-bar-chart-o\" style=\"font-size:20px;color:#999\"></i>
                                <span>Statistik</span>
                            </button>";
                }
            ?>

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
            </div>

            <button class="changePic">Change Profile Picture</button>
            <button class="edit">Edit Profile Info</button>
        </div>
    </section>

    <section class="configSect second">

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
    <button class="option about">Chat löschen</button>
</div>

<?php
$username = $Database->getCurrentUser();
$userid = $Database->getUserID();
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
<script src='./../js/vendor/jquery.min.js'></script>
<script  src="./../js/mainpage.js"></script>
</body>
</html>
