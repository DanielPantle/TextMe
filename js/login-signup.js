$(function() {

    $('#login-form-link').click(function(e) {
        $("#login-form").delay(100).fadeIn(100);
        $("#register-form").fadeOut(100);
        $('#register-form-link').removeClass('active');
        $(this).addClass('active');
        e.preventDefault();
    });
    $('#register-form-link').click(function(e) {
        $("#register-form").delay(100).fadeIn(100);
        $("#login-form").fadeOut(100);
        $('#login-form-link').removeClass('active');
        $(this).addClass('active');
        e.preventDefault();
    });

    validateLogin();
    validateRegistration();

});

/**
 * validates username and password
 * */
function validateLogin() {

    const loginUsernameField = $("#login-username");
    const loginPasswordField = $("#login-password");

    $('.btn-login').click(function (e) {
        const username = loginUsernameField.val();
        const password = loginPasswordField.val();
        if(username.length < 3) {
            e.preventDefault();
            console.log("username error");
            loginUsernameField.addClass("invalid");
            $("#username-error").show();
        }
        if(password.length < 6){
            e.preventDefault();
            console.log("password error");
            loginPasswordField.addClass("invalid");
            $("#password-error").show();
        }
    });

    loginUsernameField.keypress(function () {
        if(loginUsernameField.hasClass("invalid")) {
            loginUsernameField.removeClass("invalid");
            $("#username-error").hide();
        }
    });

    loginPasswordField.keypress(function () {
        if(loginPasswordField.hasClass("invalid")) {
            loginPasswordField.removeClass("invalid");
            $("#password-error").hide();
        }
    });
}

/**
 * validates every input field
 */
function validateRegistration() {

    const registerUsernameField = $("#register-username");
    const registerPasswordField = $("#register-password");
    const registerEmailField = $("#email");
    const registerPasswordConfirmField = $("#confirm-password");

    $('.btn-register').click(function (e) {
        const username = registerUsernameField.val();
        const password = registerPasswordField.val();
        const passwordConfirm = registerPasswordConfirmField.val();
        const email  = registerEmailField.val();

        if(username.length < 3) {
            e.preventDefault();
            console.log("username error");
            registerUsernameField.addClass("invalid");
            $("#register-username-error").show();
        }
        if(!isValidEmailAddress(email)) {
            e.preventDefault();
            console.log("email error");
            registerEmailField.addClass("invalid");
            $("#register-email-error").show();
        }

        const passwordMessage = validatePassword(password);
        if(passwordMessage !== ""){
            e.preventDefault();
            console.log(passwordMessage);
            registerPasswordField.addClass("invalid");
            $("#register-password-error").html(passwordMessage);
            $("#register-password-error").show();
        }
        if(passwordConfirm !== password){
            e.preventDefault();
            console.log("password confirm error");
            registerPasswordConfirmField.addClass("invalid");
            $("#register-password-confirm-error").show();
        }
    });

    registerUsernameField.keypress(function () {
        if(registerUsernameField.hasClass("invalid")) {
            registerUsernameField.removeClass("invalid");
            $("#register-username-error").hide();
        }
    });
    registerEmailField.keypress(function () {
        if(registerEmailField.hasClass("invalid")) {
            registerEmailField.removeClass("invalid");
            $("#register-email-error").hide();
        }
    });
    registerPasswordField.keypress(function () {
        if(registerPasswordField.hasClass("invalid")) {
            registerPasswordField.removeClass("invalid");
            $("#register-password-error").hide();
        }
    });
    registerPasswordConfirmField.keypress(function () {
        if(registerPasswordConfirmField.hasClass("invalid")) {
            registerPasswordConfirmField.removeClass("invalid");
            $("#register-password-confirm-error").hide();
        }
    });


    function isValidEmailAddress(emailAddress) {
        var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
        return pattern.test(emailAddress);
    }

    function validatePassword(str) {
            let returnMessage = "";
            if (str.length < 6) {
                returnMessage = returnMessage.concat("<li>must be at least 6 characters</li><br>");
            } if (str.search(/\d/) == -1) {
                returnMessage = returnMessage.concat("<li>must contain at least 1 digit</li><br>");
            } if (str.search(/[A-Z]/) == -1) {
                returnMessage = returnMessage.concat("<li>must contain at least 1 upper case letter</li><br>");
            } if (str.search(/[a-z]/) == -1) {
                returnMessage = returnMessage.concat("<li>must contain at least 1 lower case letter</li><br>");
            } if (str.search(/[!"#\$%&'\(\)\*\+,\-\.\/:;<=>\?@\[\]\^_`{\|}~]/) == -1) {
                returnMessage = returnMessage.concat("<li>must contain at least 1 special character</li>");
            }
            return(returnMessage);
    }
}