<!DOCTYPE html>
<html>
    <head>
<?php

include("./php/db.php");
$Database = new Database();


// Login überprüfen
if(isset($_POST['login-submit'])) {
	// Login-Form wurde abgesendet
	$username = $_POST['username'];
	$password = $_POST['password'];

	// Einloggen
	if($Database->login($username, $password)) {
		echo "<div class='alert alert-success'>Login erfolgreich!</div>";
	}
	else {
		echo "<div class='alert alert-danger'>Login fehlgeschlagen!</div>";
	}
}


// Registrierung überprüfen
if(isset($_POST['register-submit'])) {
	// Registrierungs-Form wurde abgesendet
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$confirm_password = $_POST['confirm-password'];


	// Passwörter überprüfen
	if($password != $confirm_password) {
		echo "<div class='alert alert-danger'>Registrierung fehlgeschlagen! Passwörter stimmen nicht überein!</div>";
	}
	else {
		// Benutzer-Name überprüfen (ob vorhanden)
		if($Database->userExists($username, $email)) {
			echo "<div class='alert alert-danger'>Registrierung fehlgeschlagen! E-Mail ist schon vergeben!</div>";
		}
		else {
			// Registrieren
			if($Database->register($username, $email, $password)) {
				echo "<div class='alert alert-success'>Registrierung erfolgreich!</div>";
			}
			else {
				echo "<div class='alert alert-danger'>Registrierung fehlgeschlagen!</div>";
			}
		}
	}
}


// Link prüfen und falls vorhanden speichern
if(isset($_GET['link'])) {
	$_SESSION['link'] = $_GET['link'];
	echo "<script>var linkResult = 'Bitte melde dich an, damit der Link verarbeitet werden kann!';</script>";
}



// prüfen, ob User eingeloggt ist
if($Database->isLoggedIn()) {
    // zur Chat-Seite weiterleiten
    header('Location: ./chat');


}

// Login fehlgeschlagen - Login-Seite anzeigen


?>
        <title>TextMe - Login/Registrierung</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="./css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="./css/login-signup.css">
        <link rel="icon" href="./images/Speach-BubbleDialog-512.png">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
    <noscript>
        <div id="nojavascript">
            Diese Anwendung benötitgt JavaScript zum ordungsgemäßen Betrieb.
            Bitte <a href="https://www.enable-javascript.com/" target="_blank" rel="noreferrer"> aktivieren Sie Java Script</a>
            und laden Sie die Seite neu.
        </div>
    </noscript>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-login">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="#" class="active" id="login-form-link">Login</a>
                            </div>
                            <div class="col-sm-6">
                                <a href="#" id="register-form-link">Register</a>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form id="login-form" action="index.php" method="post" role="form" style="display: block;">
                                    <div class="form-group">
                                        <input type="text" name="username" id="login-username" tabindex="1" class="form-control" placeholder="Username" value="">
                                    </div>
                                    <div class="alert alert-danger" role="alert" style=" display:none;" id="username-error">minimum three characters!</div>
                                    <div class="form-group">
                                        <input type="password" name="password" id="login-password" tabindex="2" class="form-control" placeholder="Password">
                                    </div>
                                    <div class="alert alert-danger" role="alert" style=" display:none;" id="password-error">minimum six characters!</div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 col-sm-offset-3">
                                                <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-login" value="Log In">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="text-center">
                                                    <a href="php/passwordReset.php" tabindex="5" class="forgot-password">Forgot Password?</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form id="register-form" action="" method="post" role="form" style="display: none;">
                                    <div class="form-group">
                                        <input type="text" name="username" id="register-username" tabindex="1" class="form-control" placeholder="Username" value="">
                                    </div>
                                    <div class="alert alert-danger" role="alert" style=" display:none;" id="register-username-error">minimum three characters!</div>
                                    <div class="form-group">
                                        <input type="email" name="email" id="email" tabindex="1" class="form-control" placeholder="Email Address" value="">
                                    </div>
                                    <div class="alert alert-danger" role="alert" style=" display:none;" id="register-email-error">invalid email!</div>
                                    <div class="form-group">
                                        <input type="password" name="password" id="register-password" tabindex="2" class="form-control" placeholder="Password">
                                    </div>
                                    <div class="alert alert-danger" role="alert" style=" display:none;" id="register-password-error">minimum six characters!</div>
                                    <div class="form-group">
                                        <input type="password" name="confirm-password" id="confirm-password" tabindex="2" class="form-control" placeholder="Confirm Password">
                                    </div>
                                    <div class="alert alert-danger" role="alert" style=" display:none;" id="register-password-confirm-error">passwords doesn't match!</div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 col-sm-offset-3">
                                                <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-register" value="Register Now">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cookie Popup -->
    <link rel="stylesheet" type="text/css" href="./css/cookie.css" />
    <script src="./js/cookie.js"></script>
    <script>
        window.addEventListener("load", function() {
            window.cookieconsent.initialise({
                "palette": {
                    "popup": {
                        "background": "#edeff5",
                        "text": "#838391"
                    },
                    "button": {
                        "background": "transparent",
                        "text": "#4b81e8",
                        "border": "#4b81e8"
                    }
                },
                "content": {
                    "message": "Diese Website verwendet Cookies. Informationen zu Cookies und ihrer Deaktivierung finden Sie ",
                    "dismiss": "Verstanden und weiter",
                    "link": "hier.",
                }
            });

            if(linkResult != null) {
            	alert(linkResult);
            }
        });
    </script>


    <script src="js/vendor/jquery-3.3.1.js"></script>
    <script src="js/vendor/bootstrap-4.0.0-dist/js/bootstrap.js"></script>
    <script src="js/login-signup.js"></script>
    </body>
</html>