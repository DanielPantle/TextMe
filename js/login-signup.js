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

/**
 * check username length and password length from login and prevent post by fail
 * */
    $('.btn-login').click(function (e) {
        const username = $("#username").val();
        const password = $("#password").val();
        if(username.length < 3) {
            e.preventDefault();
            console.log("username error");
            $("#username").addClass("invalid");
            jQuery("#username-error").toggle();
        }
        if(password.length < 6){
            e.preventDefault();
            console.log("password error");
            $("#password").addClass("invalid");
            jQuery("#password-error").toggle();
        }
        console.log("username: " + username + ", pw: " + password);
    });


    $("#username").click(function () {
        if($("#username").hasClass("invalid")) {
            $("#username").removeClass("invalid");
            jQuery("#username-error").toggle();
        }
    });

    $("#password").click(function () {
        if($("#password").hasClass("invalid")) {
            $("#password").removeClass("invalid");
            jQuery("#password-error").toggle();
        }
    });

});
