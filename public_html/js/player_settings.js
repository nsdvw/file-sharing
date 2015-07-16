/* Плеер получает настройки */
var suppliedFormat = [];
var splitPath = location.pathname.split('/');
var id = splitPath[2];
var settings = getPlayerSettings(id);
console.log(settings);
for (var key in settings) {
    suppliedFormat.push(key);
}

suppliedFormat = suppliedFormat.join(',');