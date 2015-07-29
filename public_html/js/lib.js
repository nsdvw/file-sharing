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

/* Function from w3.org to get a compatible path from input.value
http://www.w3.org/TR/2012/WD-html5-20121025/states-of-the-type-attribute.html#fakepath-srsly
*/
function extractFilename(path) {
    if (path.substr(0, 12) == "C:\\fakepath\\")
        return path.substr(12); // modern browser
    var x;
    x = path.lastIndexOf('/');
    if (x >= 0) // Unix-based path
        return path.substr(x+1);
    x = path.lastIndexOf('\\');
    if (x >= 0) // Windows-based path
        return path.substr(x+1);
    return path; // just the filename
}

function updateCounter() {
    var counter = document.getElementById('counter');
    counter.innerHTML = String(Number(counter.innerHTML) + 1);
}

function getPlayerSettings(id) {
    var settings = undefined;
    var xhr = getXmlHttp();
    xhr.onreadystatechange = function () {
        if (this.readyState != 4) return;
        if (this.responseText != 'error') {
            settings = JSON.parse(this.responseText);
        }
    };
    xhr.open('GET', '/ajax/finfo/' + id, false);
    xhr.send(null);
    return settings;
}

function cropFilename(fileName, maxLength) {
    maxLength = maxLength || 40;
    if (fileName.length <= maxLength) return fileName;
    return fileName.substr(0, maxLength - 3) + '...';
}
