var files = document.getElementsByClassName('file-item');
for (var i=0; i < files.length; i++) {
	files[i].addEventListener('click', function(){
		var files = document.getElementsByClassName('file-item');
		for (var i=0; i < files.length; i++) {
			removeClass(files[i], 'file-selected');
			addClass(files[i], 'file-nonselected');
		}
		removeClass(this, 'file-nonselected');
		addClass(this, 'file-selected');

		if (this.hasChildNodes()) {
			var icons = document.getElementsByClassName('file-icon');
			for (var k=0; k < icons.length; k++) {
				removeClass(icons[k], 'file-icon-revert');
			}
			for (var j=0; j < this.childNodes.length; j++) {
				var currentChild = this.childNodes[j];
				if (currentChild.nodeType !== 1) continue;
				if (currentChild.hasAttribute('class') &&
					hasClass(currentChild, 'file-icon')) {
						console.log(currentChild);
						addClass(currentChild, 'file-icon-revert');
				}
			}
		}

		var xhr = getXmlHttp();
		if (!this.hasAttribute('data-id')) return;
		var id = this.getAttribute('data-id');
	    xhr.onreadystatechange = function () {
	        if (this.readyState != 4) return;
	        var response = JSON.parse(this.responseText);
	        if (response) {
	            createPreview(response);
	        }
	    };
	    xhr.open('GET', '/ajax/fileinfo/' + id, true);
	    xhr.send(null);
	});
}
