
<?php

include("php/db.php");
$Database = new Database();


// Login überprüfen
if(isset($_POST['login-submit'])) {
	// Login-Form wurde abgesendet
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	// Einloggen
	if($Database->login($username, $password)) {
		echo "<h3>Login erfolgreich!</h3>";
	}
	else {
		echo "<h3>Login fehlgeschlagen!</h3>";
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
		echo "<h3>Registrierung fehlgeschlagen! Passwörter stimmen nicht überein!</h3>";
	}
	else {
		// Benutzer-Name überprüfen (ob vorhanden)
		if($Database->userExists($username, $email)) {
			echo "<h3>Registrierung fehlgeschlagen! E-Mail ist schon vergeben!</h3>";
		}
		else {
			// Registrieren
			if($Database->register($username, $email, $password)) {
				echo "<h3>Registrierung erfolgreich!</h3>";
			}
			else {
				echo "<h3>Registrierung fehlgeschlagen!</h3>";
			}
		}
	}
}


// prüfen, ob User eingeloggt ist
if($Database->isLoggedIn()) {
    // zur Chat-Seite weiterleiten
    header('Location: /chat');


}

// Login fehlgeschlagen - Login-Seite anzeigen


?>

<head>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/login-signup.css">
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js"></script>-->
    <script src="js/vendor/jquery-3.3.1.js"></script>
    <script src="js/vendor/bootstrap-4.0.0-dist/js/bootstrap.js"></script>
    <script src="js/login-signup.js"></script>
</head>
<body>

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
                            <form id="login-form" action="/chat/index.php" method="post" role="form" style="display: block;">
                                <div class="form-group">
                                    <input type="text" name="username" id="login-username" tabindex="1" class="form-control" placeholder="Username" value="">
                                </div>
                                <div class="alert alert-danger" role="alert" style=" display:none;" id="username-error">minimum three characters!</div>
                                <div class="form-group">
                                    <input type="password" name="password" id="login-password" tabindex="2" class="form-control" placeholder="Password">
                                </div>
                                <div class="alert alert-danger" role="alert" style=" display:none;" id="password-error">minimum six characters!</div>
                                <div class="form-group text-center">
                                    <input type="checkbox" tabindex="3" class="" name="remember" id="remember">
                                    <label for="remember"> Remember Me</label>
                                </div>
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
                                                <a href="#" tabindex="5" class="forgot-password">Forgot Password?</a>
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
</body>