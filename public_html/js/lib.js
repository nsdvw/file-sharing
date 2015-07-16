function getXmlHttp()
{
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

function updateCounter()
{
    var counter = document.getElementById('counter');
    counter.innerHTML = String(Number(counter.innerHTML) + 1);
}

function getPlayerSettings(id)
{
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
