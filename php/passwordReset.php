<?php

include("db.php");
$Database = new Database();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'mail/src/Exception.php';
require 'mail/src/PHPMailer.php';
require 'mail/src/SMTP.php';


$generatedPassword = generatePassword();

if (isset($_POST) & !empty($_POST)) {
    $mail = $_POST['email'];
    $userExists = $Database->userExists("", $mail);
    if ($userExists == 1) {
        if ($Database->changePasswordByEmail($mail, $generatedPassword)) {
            //echo "<div class='alert alert-info'>PW:[" . $generatedPassword . "]</div>";
            sendPasswordByMail($mail, $generatedPassword);
        }
    } else {
        echo "<div  class='alert alert-danger'>E-Mail does not exist in database</div>";
    }
}

function sendPasswordByMail($to, $password)
{
    $mail = new PHPMailer(true);
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
    $mail->SMTPDebug = 0;
//Set the hostname of the mail server
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
    $mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = "peterpandreamworld666@gmail.com";
//Password to use for SMTP authentication
    $mail->Password = "Pineapple#1";
//Set who the message is to be sent from
    $mail->setFrom('peterpandreamworld666@gmail.com', 'TextME');
//Set who the message is to be sent to
    $mail->addAddress($to, '');
//Set the subject line
    $mail->Subject = 'Your Recovered TextMe Password';
    $mail->Body = 'Please use this password to login ['. $password .']';
//send the message, check for errors
    if (!$mail->send()) {
        echo "<div class='alert alert-danger'>Failed to Recover your password, try again</div>";
    } else {
        echo "<div class='alert alert-info'>Your Password has been sent to your email</div>";
    }

}

function generatePassword($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!§$%&?';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }
    return $result;
}


?>
<head>

    <title>TextMe - Passwort vergessen..</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <script src="../js/vendor/jquery-3.3.1.js"></script>
    <link rel="icon" href="./../images/Speach-BubbleDialog-512.png">
    <script src="../js/vendor/bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!------ Include the above in your HEAD tag ---------->
<noscript>
    <div id="nojavascript">
        Diese Anwendung benötitgt JavaScript zum ordungsgemäßen Betrieb.
        Bitte <a href="https://www.enable-javascript.com/" target="_blank" rel="noreferrer"> aktivieren Sie Java
            Script</a>
        und laden Sie die Seite neu.
    </div>
</noscript>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" href="../css/password-reset.css">
<div class="form-gap"></div>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="text-center">
                        <h3><i class="fa fa-lock fa-4x"></i></h3>
                        <h2 class="text-center">Forgot Password?</h2>
                        <p>You can reset your password here.</p>
                        <div class="panel-body">

                            <form id="register-form" role="form" autocomplete="off" class="form" method="post">

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                                        <input id="email" name="email" placeholder="email address" class="form-control" type="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input name="recover-submit" class="btn btn-lg btn-primary btn-block"
                                           value="Reset Password" type="submit">
                                </div>
                                <div class="text-center">
                                    <a href="../../" tabindex="5" class="return-to-mainpage">Return to Mainpage</a>
                                </div>

                                <input type="hidden" class="hide" name="token" id="token" value="">
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
