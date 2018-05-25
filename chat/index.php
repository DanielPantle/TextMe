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

if(isset($_POST['editSubmitButton'])) {

    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];

    $username = $Database->getCurrentUser();
    $userid = $Database->getUserID();
    $email = $Database->getEmail($userid);

    if($Database->login($username, $currentPassword)) {
        if($newPassword != "" && $Database->changePasswordByEmail($email,$newPassword)) {
            echo "<div class='alert alert-success'>Password changed Successfully!</div>";
        } else {
            echo "failure!";
        }
    } else {
        echo "False current Password!";
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
        $current_uid =$Database->getUserID();

        if($res && $res > 0) {
            echo "<script>var linkResult = 'Du wurdest erfolgreich dem Chat $chatName hinzugefügt. (Eingeladen von $invitor)';</script>";
            $username_link = $Database->getCurrentUser();
            $username_link = $username_link[0][0];
            $message_link =" wurde dem Chat hinzugefügt";
            $Database->writeMessage($cid,$current_uid,$message_link);
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
                for ($j=0;$j<$count;$j++) {
                    $deleted[$j] = $Database->isChatDeletedForUser($chats[$j]['cid']);
                    //print_r($members_2[$j]);
                    //$deleted[$j]=1;
                    if (!$deleted[$j]) {
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
                            $members_picture="";
                            if($members_20=="NV"){
                                $members_0="--> kein Chat Partner vorhanden <--";
                            }else {
                                $username = $Database->getCurrentUser();
                                if($members_10==$username){
                                    $members_0 = "An: ".$members_20;
                                    $members_picture=$members_20;
                                }else {
                                    $members_0 = "An: ".$members_10;
                                    $members_picture=$members_10;
                                }
                            }
                            $image = "";
                            if(!$members_picture==""){
                                $userid_members_picture = $Database->getUserIdByName($members_picture);
                                $picture = $Database->showPictureByUserId($userid_members_picture);
                                $count_picture = count($picture);
                                if($count_picture>0){
                                    $bild= $picture[0]['imgdata'];
                                    $image =  '<div class= "imagenew" style="background: #FFF url(data:image;base64,'.$bild.') no-repeat center;background-size:cover"></div>';
                                }else {
                                    $image = '<div class= "imagenew" style="background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover"></div>';
                                }
                            }


                            echo "<div class='chatButton' onclick='chatButtonClick($cid[$j],\"$chatname[$j]\",\"$members_2[$j]\",\"$history\");'> 
                                        
                                    
                                                ".$image." 
                                        <div class='chatInfo'> 
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
                            if($nachricht==" wurde dem Chat hinzugefügt"){
                                $return =$return."<div class=\\\"chatVerlassen\\\"><b>$name</b>$nachricht</div>";
                            }else if($nachricht==" hat den Chat verlassen"){
                                $return =$return."<div class=\\\"chatVerlassen\\\"><b>$name</b>$nachricht</div>";
                            }else {
                                if($username==$name){
                                    $return = $return."<div class=\\\"msg messageSent\\\">$nachricht<span class=\\\"timestamp\\\">$zeit</span></div>";
                                }
                                else if($count_2>2){
                                    $userid_members_picture = $Database->getUserIdByName($name);
                                    $picture = $Database->showPictureByUserId($userid_members_picture);
                                    $count = count($picture);
                                    if($count>0){
                                        $bild= $picture[0]['imgdata'];
                                        $return = $return. "<div class= \\\"msgimage\\\" style=\\\"background: #FFF url(data:image;base64,".$bild.") no-repeat center;background-size:cover\\\"></div>";
                                    }else {
                                        $return = $return."<div class= \\\"msgimage\\\" style=\\\"background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover\\\"></div>";
                                    }
                                    $return = $return."<div class=\\\"msg messageReceivedGroup\\\">$name: $nachricht<span class=\\\"timestamp\\\">$zeit</span></div>";
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
                <?php
                    $picture = $Database->showPictureFromCurrentUser();
                    $count = count($picture);
                    if($count>0){
                        $bild= $picture[0]['imgdata'];
                        echo '<div class= "image" style="background: #FFF url(data:image;base64,'.$bild.') no-repeat center;background-size:cover"></div>';
                    }else {
                        echo '<div class= "image" style="background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover"></div>';
                    }
                ?>
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
            <button class="ng">
                <i class="material-icons">&#xE0B6;</i>
                <span>Random-Chat erstellen</span>
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
            <p class="confTitle">Einstellungen</p>

            <?php
            $picture = $Database->showPictureFromCurrentUser();
            $count = count($picture);
            if($count>0){
                $bild= $picture[0]['imgdata'];
                echo '<div class= "image" style="background: #FFF url(data:image;base64,'.$bild.') no-repeat center;background-size:cover"></div>';
            }else {
                echo '<div class= "image" style="background: #FFF url(./../images/Profilbild_default.jpg) no-repeat center;background-size:cover"></div>';
            }
            ?>

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
        </form>

        <br>
        <div class="editPersonalInfoDiv" style="display: none">
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
                //echo "user hat ein bild";
                //echo $result;
            }else {
                $result = $Database->createPictureFromUser($name,$image);
                //echo "user hat kein bild";
                //echo $result;
            }
        }
    }
    $result = $Database->showPictureFromCurrentUser();
    //print_r($result);
    $count = count($result);
    if($count>0){
        $bild= $result[0]['imgdata'];
        //echo '<img  src="data:image;base64,'.$bild.'">';
    }else //echo '<img src="./../images/Profilbild_default.jpg">'
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

<?php
$username = $Database->getCurrentUser();
$userid = $Database->getUserID();
$email = $Database->getEmail($userid);

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
