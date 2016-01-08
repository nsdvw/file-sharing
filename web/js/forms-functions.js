function showErrorMessage(errorMessage) {
    var alertBox = $(".alert.alert-danger");
    alertBox.text(errorMessage);
    alertBox.slideDown();
}

function isEmpty(input) {
    if (input.val() == "") {
        return true;
    }
    return false;
}

function isNotValidEmail(email) {
    if ( email.val().search(/^\S+@\S+\.\S+$/) != 0) {
        return true;
    }
    return false;
}

function notMatch(password, repeatPassword) {
    if (password.val() != repeatPassword.val()) {
        return true;
    }
    return false;
}