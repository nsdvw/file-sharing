$(function () {
    var errorBox = $("#errorBox");
    var successBox = $("#successBox");
    successBox.hide().text("");
    errorBox.hide().text("");

    $("#editForm").on("submit", function (event) {
        event.preventDefault();
        var form = document.forms.edit;
        var formData = new FormData(form);
        $.ajax({
            "method": "POST",
            "url": "/edit/" + window.location.pathname.split("/").pop() + "?ajax",
            "data": formData,
            "dataType": "json",
            "processData": false,
            "contentType": false
        }).done(function (response) {
            if (response.error) {
                errorBox.text(response.error).slideUp();
            } else {
                var description = response.text.description;
                var date = new Date(response.text.date);
                var expire = Math.floor(
                    (date.getTime() - Date.now()) / (1000*3600*24)
                );
                $("#expireLabel").text("Storage time expires in " + expire + " days");
                $("#description").val(description);
                successBox.text("Updated successfully").slideDown();
            }
        });
    });

});
