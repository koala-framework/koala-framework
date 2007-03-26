E3.Component.Textbox = function(componentId, class) {
    E3.Component.Textbox.superclass.constructor.call(this, componentId, class);
};
YAHOO.lang.extend(E3.Component.Textbox, E3.Component.Abstract);

E3.Component.Textbox.prototype.handleClick = function(o, e) {
	var el = e.el;
    if (el.hasChildNodes) { 
        var child = el.get('firstChild');
        el.removeChild(child); 
	}
	e.scope.textbox = document.createElement('input');
	if(child.data) e.scope.textbox.value = child.data;
	el.appendChild(e.scope.textbox);
	
	E3.Component.Textbox.superclass.handleClick.call(this, o, e); 
};

E3.Component.Textbox.prototype.handleSave = function(o, e) {
    var data = 'content='+encodeURIComponent(this.textbox.value);
    this.textbox = null;
	YAHOO.util.Connect.asyncRequest('POST', '/ajax/fe?save=1&componentId='+this.componentId+'&componentClass='+this.class, this, data);
};
