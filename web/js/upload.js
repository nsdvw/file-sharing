/* i didn't find the jquery onprogress handler, so use native js  */
function getXmlHttp() {
    var xmlhttp;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            xmlhttp = false;
        }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

var uploadButton = $("#uploadButton");
uploadButton.on("click", function (event) {
    event.preventDefault();
    $(this).hide();
    var agreeBox = $("#agreeBox");
    var errorBox = $("#uploadForm .text-danger");
    errorBox.text("");
    if (!agreeBox.prop("checked")) {
        errorBox.text("You must agree with TOS");
        $(this).show();
        return;
    }
    var form = document.forms.upload;
    var file1 = $("#file-1")[0];
    if (!file1.value) {
        errorBox.text("You didn't choose a file");
        $(this).show();
        return;
    }
    var xhr = getXmlHttp();
    var formData = new FormData(form);
    var progressBox = $(".progress");
    var progressBar = $(".progress-bar");
    progressBox.show();

    xhr.onreadystatechange = function () {
        if (this.readyState != 4) return;
        var response = JSON.parse(this.responseText);
        if (response.error !== null) {
            errorBox.text(response.error);
            console.log(response);
        } else {
            var id = response.text;
            window.location.href = decodeURI('/view/' + id);
        }
    };

    xhr.upload.onprogress = function (event) {
        var progress = Math.ceil(event.loaded / event.total * 100) + "%";
        progressBar.css("width", progress);
        progressBar.text(progress);
    };

    xhr.open('POST', '/?ajax');
    xhr.send(formData);
});
