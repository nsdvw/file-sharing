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
                var author = response.text.author;
                appendComment("#commentTemplate", $(form), comment, author);
                form.reset();
                refreshCaptcha();
            }
        }).fail(function (response) {
            errorBox.text("Server error " + response.status + ", please try again later");
        });
    });
});

function appendComment(templateSelector, form, comment, author) {
    var template = $(templateSelector).html();
    for (var i = 0; i < 2; i++) {
        var indexOfLevel = template.indexOf("{level}");
        template = template.substring(0, indexOfLevel)
                + comment.level
                + template.substring(indexOfLevel + 7);
    }
    template = $(template);
    author = author || 'Anonymous';
    $(".media-heading", template).text(author);
    $(".comment-text", template).text(comment.contents);
    $(".added", template).text(comment.added);
    $(".reply", template)
        .attr("href", "/view/" + comment.file_id + "?reply=" + comment.id)
        .attr("data-reply-id", comment.id)
        .html("#" + comment.id + " Reply");
    if ( form.is(":first-child") ) {
        $("#comments").append(template);
    } else {
        var level = form.prev().data("level");
        template.insertBefore(
            $("#commentForm ~ .media[data-level=" + level + "]")
        );
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
