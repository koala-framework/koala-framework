E3.Component.Paragraphs = function(componentId) {
    E3.Component.Paragraphs.superclass.constructor.call(this, componentId);
    
};
YAHOO.lang.extend(E3.Component.Paragraphs, E3.Component.Abstract);

E3.Component.Paragraphs.prototype.nextSibling = function(container) {
	while (container != null) {
    	container = container.nextSibling;
    	if (container != null && container.nodeType == 1) { return container; }
    }
    return null;
}

E3.Component.Paragraphs.prototype.previousSibling = function(container) {
	while (container != null) {
    	container = container.previousSibling;
    	if (container != null && container.nodeType == 1) { return container; }
    }
    return null;
}

E3.Component.Paragraphs.prototype.moveUp = function(o, e) {
	el1 = e.el1;
	el2 = this.previousSibling(el1);
	if (el2 != null) {
		el1.parentNode.insertBefore(el1, el2);
	}
	this.checkButtons();
}

E3.Component.Paragraphs.prototype.moveDown = function(o, e) {
	el1 = e.el1;
	el2 = this.nextSibling(el1);
	if (el2 != null) {
		el1.parentNode.insertBefore(el2, el1);
	}
	this.checkButtons();
}

E3.Component.Paragraphs.prototype.checkButtons = function() {
	for (var i = 1; i < this.containers.length; i++) {
		var c = this.containers[i];
		if (this.nextSibling(c['container']) == null) {
			c['button_down']._setDisabled(true);
		} else {
			c['button_down']._setDisabled(false);
		}
		if (this.previousSibling(c['container']) == null) {
			c['button_up']._setDisabled(true);
		} else {
			c['button_up']._setDisabled(false);
		}
	}
}

E3.Component.Paragraphs.prototype.init = function() {
	this.containers = new Array();
	x = 1;
	containername = 'container_' + this.componentId + '_' + x;
	while (document.getElementById(containername) != null) {
		this.containers[x] = new Object();
		this.containers[x]['container'] = document.getElementById(containername);
		x++;
		containername = 'container_' + this.componentId + '_' + x;
	}
	for (var i = 1; i < this.containers.length; i++) {
		container = this.containers[i]['container'];
		//var dd = new YAHOO.util.DD(container);

		var moveUpButton = new YAHOO.widget.Button(
			{id:container.id + '_button_up', label: 'Up', container: container}
		);
		var moveDownButton = new YAHOO.widget.Button(
			{id:container.id + '_button_down', label: 'Down', container: container}
		);
		moveUpButton.on('click', this.moveUp, {el1:container, button:moveUpButton}, this);
		moveDownButton.on('click', this.moveDown, {el1:container, button:moveDownButton}, this);

		this.containers[i]['button_up'] = moveUpButton;
		this.containers[i]['button_down'] = moveDownButton;
	}
	this.checkButtons();
}
