$(function () {
    var form = $("#commentForm");

    form.on("submit", function (event) {
        event.preventDefault();
        $(":submit", form).prop("disabled", true);
        var errorBox = $("#errorMessage");
        if (!validateCommentForm()) {
            return;
        }
        var replyID = $("[name='comment_form[reply_id]']", form).val();
        var formData = {
            "comment_form[contents]": $("[name='comment_form[contents]']", form).val(),
            "comment_form[reply_id]": replyID,
            "comment_form[captcha]": $("[name='comment_form[captcha]']", form).val()
        };
        showError("", errorBox);
        $.ajax({
            "method": "POST",
            "url": "/view/" + fileID +
                "?ajax&reply=" + replyID,
            "data": formData,
            "dataType": "json"
        }).done(function (response) {
            if (response.error) {
                showError(response.error, errorBox);
                refreshCaptcha();
            } else {
                var comment = response.text.comment;
                var login = response.text.login;
                appendComment("#commentTemplate", form, comment, login);
                form.trigger("reset");
                refreshCaptcha();
            }
        }).fail(function (response) {
            var msg = "Server error " + response.status + ", please try again later";
            showError(msg, errorBox);
        });
        $(":submit", form).prop("disabled", false);
    });

    $("#downloadLink").on("click", function (event) {
        var oldCounter = $("#downloadCounter").html();
        $("#downloadCounter").html(++oldCounter);
    });

});

function appendComment(templateSelector, form, comment, login) {
    login = login || 'Anonymous';
    var source = $(templateSelector).html();
    var template = Handlebars.compile(source);
    var context = {
        "level": comment.level,
        "login": login,
        "contents": comment.contents,
        "added": comment.added,
        "file_id": comment.file_id,
        "comment_id": comment.id
    };
    var html = template(context);
    if ( form.is(":first-child") ) {
        $("#comments").append(html);
    } else {
        var level = +form.prev().data("level");
        var currentEl = form.next();
        while (currentEl.length > 0) {
            if (currentEl.data("level") <= level) {
                $(html).insertBefore(currentEl);
                return;
            }
            currentEl = currentEl.next();
        }
        $("#comments").append(html);
    }
}

function validateCommentForm() {
    var form = $("#commentForm");
    var errorBox = $("#errorMessage");
    var textarea = $(".comment-area", form).val();
    var captcha = $(":text", form).val();
    if (textarea == "") {
        showError("Message is empty!", errorBox);
        return false;
    } else if (captcha == "") {
        showError("Captcha required", errorBox);
        return false;
    } else if (textarea.length > 10000) {
        showError("Message is too long", errorBox);
        return false;
    }
    return true;
}

function refreshCaptcha() {
    $("#captcha-img").attr("src", "/image/captcha_generator.php");
}

function showError(msg, msgBox) {
    msgBox = msgBox || $("#errorMessage");
    msgBox.text(msg);
}
