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
				if (currentChild.nodeType == 1) {
					if (currentChild.hasAttribute('class') &&
						currentChild.getAttribute('class').indexOf('file-icon') != -1) {
						addClass(currentChild, 'file-icon-revert');
					}
				}
			}
		}
	});
}
