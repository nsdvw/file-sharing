$(function () {

    $(".reply").on("click", function (event) {
        event.preventDefault();
        var replyID = $(this).data("reply-id");
        $("#commentForm .replyID").attr("value", replyID);
        $("#commentForm").insertAfter(
            $(".media:has(a[data-reply-id=" + replyID + "])")
        );
        var replyButton = $("#reply");
        replyButton.attr("data-reply-id", replyID).removeClass("hidden");
        $(".reply-container", replyButton).text("Reply to #" + replyID);
    });

    $("#commentForm .glyphicon-remove").on("click", function (event) {
        $("#commentForm").prependTo("#comments");
        $("#commentForm .replyID").attr("value", "");
        var replyButton = $("#reply");
        replyButton.addClass("hidden");
    });

});
