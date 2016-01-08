$(function () {
    var alertBox = $(".alert.alert-danger");
    if(alertBox.text() == "") {
        alertBox.hide();
    }
    $("#email, #password").on("blur", validateLoginForm);
});

function validateLoginForm() {
    var email = $("#email");
    var password = $("#password");
    var alertBox = $(".alert.alert-danger");
    $("#loginForm").children().removeClass("has-error");
    alertBox.hide();
    if (isEmpty( email )) {
        email.parents(".form-group").addClass("has-error");
        showErrorMessage("Email is a required field");
    } else if (isNotValidEmail( email )) {
        email.parents(".form-group").addClass("has-error");
        showErrorMessage("Email is not valid");
    } else if (isEmpty(password)) {
        password.parents(".form-group").addClass("has-error");
        showErrorMessage("Password is a required field");
    } else {
        $("#loginForm").children().removeClass("has-error");
        alertBox.text("");
        alertBox.slideUp();
    }
}
