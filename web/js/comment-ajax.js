$(function () {
    $("#commentForm").on("submit", function (event) {
        event.preventDefault();
        var errorBox = $("#commentForm .text-danger");
        if (!validateCommentForm()) {
            return;
        }
        var form = document.forms.comment_form;
        var formData = new FormData(form);
        var replyID = form.querySelector(".replyID").value;
        errorBox.text("");
        $.ajax({
            "method": "POST",
            "url": "/view/" + window.location.pathname.split("/").pop() +
                "?ajax&reply=" + replyID,
            "data": formData,
            "dataType": "json",
            "processData": false,
            "contentType": false
        }).done(function (response) {
            if (response.error) {
                errorBox.text(response.error);
                refreshCaptcha();
            } else {
                var comment = response.text.comment;
                var login = response.text.login;
                appendComment("#commentTemplate", $(form), comment, login);
                form.reset();
                refreshCaptcha();
            }
        }).fail(function (response) {
            errorBox.text("Server error " + response.status + ", please try again later");
        });
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
    var errorBox = $("#commentForm .text-danger");
    var textarea = $("#commentForm .comment-area").val();
    var captcha = $("#commentForm input[type=text]").val();
    if (textarea == "") {
        errorBox.text("Message is empty!");
        return false;
    } else if (captcha == "") {
        errorBox.text("Captcha required");
        return false;
    } else if (textarea.length > 10000) {
        errorBox.text("Message is too long");
        return false;
    }
    return true;
}

function refreshCaptcha() {
    $("#captcha-img").attr("src", "/image/captcha_generator.php");
}
