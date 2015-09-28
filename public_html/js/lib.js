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

/* Function from w3.org to get a compatible path from fileInput.value
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

function hasClass(el, className) {
    var array = el.className.split(/\s/);
    if (array.indexOf(className) === -1) return false;
    return true; 
}

function addClass(el, addClass) {
    var oldClassName = (el.hasAttribute('class')) ? el.getAttribute('class') : '';
    if (hasClass(el, addClass)) return;
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
    var template = document.getElementById('tpl-preview').innerHTML;
    if (['image/jpeg', 'image/gif', 'image/png'].indexOf(file.mime_type) < 0) {
        template = template.replace(/<img.+class="preview-image".+/, '');
        template = template.replace(/<div.+class="preview-resolution".+<\/div>/, '');
    } else {
        template = template.replace(/\(#file.resolution#\)/g,
            file.mediaInfo.resolution_x + ' x ' + file.mediaInfo.resolution_y);
    }
    template = template.replace(/\(#file.id#\)/g, file.id);
    template = template.replace(/\(#file.name#\)/g, escapeHtml(file.name));
    template = template.replace(/\(#file.size#\)/g, file.size);
    template = template.replace(/\(#file.upload_time#\)/g, file.upload_time);
    template = template.replace(/\(#file.download_counter#\)/g, file.download_counter);
    template = template.replace(/\(#file.mime_type#\)/g, file.mime_type);
    previewBox.innerHTML = template;
}

function escapeHtml(text) {
  return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}
