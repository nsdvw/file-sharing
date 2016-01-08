$(function () {
    var alertBox = $(".alert.alert-danger");
    if(alertBox.text() == "") {
        alertBox.hide();
    }
    var email = $("#email");
    var password = $("#password");
    email.on("blur", validateLoginForm);
    password.on("blur", validateLoginForm);
});

function validateLoginForm() {
    var email = $("#email");
    var password = $("#password");
    var alertBox = $(".alert.alert-danger");
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
        email.parents(".form-group").removeClass("has-error");
        password.parents(".form-group").removeClass("has-error");
        alertBox.text("");
        alertBox.slideUp();
    }
}
