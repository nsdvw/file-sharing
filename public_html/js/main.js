window.onload = function () {
    var button = document.getElementById('send');

    button.onclick = function (event) {
        event.preventDefault();
        var notice = document.getElementById('notice');
        var error = document.getElementById('upload-error');
        if (!window.FormData) {
            notice.innerHTML = 'Uploading is in porgress, do not close the browser...';
            return;
        }
        var agreeBox = document.getElementById('agreeBox');
        if (!agreeBox.checked) {
            error.innerHTML = 'You must agree with TOS';
            return;
        } 
        var form = document.forms.upload;
        var file = document.getElementById('file1');
        error.innerHTML = '';
        if (file.value == '') {
            error.innerHTML = 'You didn\'t choose the file';
            return;
        }
        notice.innerHTML = 'Uploading is in porgress, do not close the browser...';
        var xhr = getXmlHttp();
        var formData = new FormData(form);
        var progressBox = document.getElementById('progressBox');
        var progressBar = document.getElementById('progressBar');
        progressBox.style.display = 'block';

        xhr.onreadystatechange = function () {
            if (this.readyState != 4) return;
            if (this.responseText != 'error') {
                notice.innerHTML = 'File has been uploaded successfully';
                alert('File has been uploaded successfully');
                window.location.href = decodeURI('/view?upload=ok');
            } else {
                error.innerHTML = 'Error. Try again later.';
            }
        };

        xhr.upload.onprogress = function (event) {
            var progress = event.loaded / event.total;
            var fullWidth = +(progressBox.offsetWidth);
            progressBar.style.width = fullWidth * progress + 'px';
        };

        xhr.open('POST', '/ajax/upload');
        xhr.send(formData);
    };

    var fileInput = document.getElementById('file1');
    fileInput.addEventListener('change', function(){
        var path = extractFilename(this.value);
        var fakeInputFile = document.getElementById('inputFileName');
        var pathParts = path.split('\\');
        fakeInputFile.innerHTML = cropFilename(pathParts[pathParts.length - 1]);
    });
};
