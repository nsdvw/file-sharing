$(function () {
    var commentForm = $("#commentForm");
    var replyButton = $("#reply");

    $("#comments").on("click", ".reply", function (event) {
        event.preventDefault();
        var replyID = $(this).data("reply-id");
        $(".replyID", commentForm).attr("value", replyID);
        commentForm.insertAfter(
            $(".media:has(a[data-reply-id=" + replyID + "])")
        );
        replyButton.attr("data-reply-id", replyID).removeClass("hidden");
        $(".reply-container", replyButton).text("Reply to #" + replyID);
    });

    $(".glyphicon-remove", commentForm).on("click", function (event) {
        commentForm.prependTo("#comments");
        $(".replyID", commentForm).attr("value", "");
        replyButton.addClass("hidden");
    });
});
