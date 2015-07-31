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

function addClass(el, addClass) {
    var oldClassName = (el.hasAttribute('class')) ? el.getAttribute('class') : '';
    if (oldClassName.indexOf(addClass) > -1) return;
    var newClassName = oldClassName + ' ' + addClass;
    el.setAttribute('class', newClassName);
}

function removeClass(el, removeClass) {
    var oldClassName = (el.hasAttribute('class')) ? el.getAttribute('class') : '';
    var indexOfClass = oldClassName.indexOf(removeClass);
    if (indexOfClass < 0) return;
    var newClassName = oldClassName.slice(0, indexOfClass) + 
                       oldClassName.slice(indexOfClass + removeClass.length);
    el.setAttribute('class', newClassName);
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
    xhr.open('GET', '/ajax/mediainfo/' + id, false);
    xhr.send(null);
    return settings;
}

function cropFilename(fileName, maxLength) {
    maxLength = maxLength || 40;
    if (fileName.length <= maxLength) return fileName;
    return fileName.substr(0, maxLength - 3) + '...';
}

function createPreview(file) {
    var previewBox = document.getElementById('previewBox');
    while (true) {
        if (previewBox.hasChildNodes()) {
            previewBox.removeChild(previewBox.firstChild);
        } else break;
    }

    if (['image/jpeg', 'image/gif', 'image/png'].indexOf(file.mime_type) >= 0) {
        var img = document.createElement('img');
        img.setAttribute('src', '/preview/' + file.id + '.txt');
        img.setAttribute('alt', 'preview');
        previewBox.appendChild(img);
    }

    var previewEl = document.createElement('div');
    previewEl.setAttribute('class', 'preview-name');
    var link = document.createElement('a');
    link.setAttribute('href', '/view/' + file.id);
    link.setAttribute('target', '_blank');
    var text = document.createTextNode(cropFilename(file.name, 30));
    link.appendChild(text);
    previewEl.appendChild(link);
    previewBox.appendChild(previewEl);

    previewEl = document.createElement('div');
    previewEl.setAttribute('class', 'preview-size');
    text = document.createTextNode('Size: ' + file.size);
    previewEl.appendChild(text);
    previewBox.appendChild(previewEl);

    previewEl = document.createElement('div');
    previewEl.setAttribute('class', 'preview-date');
    text = document.createTextNode('Uploaded: ' + file.upload_time);
    previewEl.appendChild(text);
    previewBox.appendChild(previewEl);

    previewEl = document.createElement('div');
    previewEl.setAttribute('class', 'preview-downloads');
    text = document.createTextNode('Downloads: ' + file.download_counter);
    previewEl.appendChild(text);
    previewBox.appendChild(previewEl);

    previewEl = document.createElement('div');
    previewEl.setAttribute('class', 'preview-format');
    text = document.createTextNode('Format: ' + file.mime_type);
    previewEl.appendChild(text);
    previewBox.appendChild(previewEl);

    if (['image/jpeg', 'image/gif', 'image/png'].indexOf(file.mime_type) >= 0) {
        previewEl = document.createElement('div');
        previewEl.setAttribute('class', 'preview-resolution');
        text = document.createTextNode('Resolution: ' + file.mediaInfo.resolution_x
                + ' x ' + file.mediaInfo.resolution_y);
        previewEl.appendChild(text);
        previewBox.appendChild(previewEl);
    }

    previewEl = document.createElement('div');
    previewEl.setAttribute('class', 'preview-more');
    link = document.createElement('a');
    link.setAttribute('href', '/view/' + file.id);
    link.setAttribute('target', '_blank');
    text = document.createTextNode('more...');
    link.appendChild(text);
    previewEl.appendChild(link);
    previewBox.appendChild(previewEl);
}
