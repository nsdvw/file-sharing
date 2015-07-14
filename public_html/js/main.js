function getXmlHttp () {
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

window.onload = function () {
    var button = document.getElementById('send');
    button.onclick = function (event) {
        var notice = document.getElementById('notice');
        if (!window.FormData) {
            notice.innerHTML = 'Идет загрузка, не закрывайте браузер...';
            return;
        }
        event.preventDefault();
        var form = document.forms.upload;
        var file = document.getElementById('file1');
        var error = document.getElementById('error');
        error.innerHTML = '';
        if (file.value == '') {
            error.innerHTML = 'Вы не выбрали файл.';
            return false;
        }
        notice.innerHTML = 'Идет загрузка, не закрывайте браузер...';
        var xhr = getXmlHttp();
        var formData = new FormData(form);
        var progressBox = document.getElementById('progressBox');
        var progressBar = document.getElementById('progressBar');

        xhr.onreadystatechange = function () {
            if (this.readyState != 4) return;
            if (this.responseText != 'error') {
                notice.innerHTML = 'Файл успешно загружен!';
            } else {
                error.innerHTML = 'Произошла ошибка. Попробуйте еще раз.';
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
};