$(function () {
    var alertBox = $(".alert.alert-danger");
    if(alertBox.text() == "") {
        alertBox.hide();
    }
    $("#email, #password, #login, #repeat-password")
        .on("blur", validateRegisterForm);
});

function validateRegisterForm() {
    var email = $("#email");
    var password = $("#password");
    var repeatPassword = $("#repeat-password");
    var login = $("#login");
    var alertBox = $(".alert.alert-danger");
    $("#registerForm").children().removeClass("has-error");
    alertBox.hide();
    if (isEmpty( email )) {
        email.parents(".form-group").addClass("has-error");
        showErrorMessage("Email is a required field");
    } else if (isNotValidEmail( email )) {
        email.parents(".form-group").addClass("has-error");
        showErrorMessage("Email is not valid");
    } else if (isEmpty(login)) {
        login.parents(".form-group").addClass("has-error");
        showErrorMessage("Login is a required field");
    } else if (isEmpty(password)) {
        password.parents(".form-group").addClass("has-error");
        showErrorMessage("Password is a required field");
    } else if (isEmpty(repeatPassword)) {
        repeatPassword.parents(".form-group").addClass("has-error");
        showErrorMessage("Repeat password");
    } else if (notMatch(password, repeatPassword)) {
        repeatPassword.parents(".form-group").addClass("has-error");
        showErrorMessage("Passwords doesn't match");
    } else {
        $("#registerForm").children().removeClass("has-error");
        alertBox.text("");
        alertBox.slideUp();
    }
}
