E3.Component.Textbox = function(componentId) {
    E3.Component.Textbox.superclass.constructor.call(this, componentId);
};
YAHOO.lang.extend(E3.Component.Textbox, E3.Component.Abstract);

E3.Component.Textbox.prototype.handleClick = function(o, e) {
	el = e.el;
    if (el.hasChildNodes) { 
        el.removeChild(el.get('firstChild')); 
	}
	child = new YAHOO.util.Element(document.createElement('input'));
	el.appendChild(child);
	
	E3.Component.Textbox.superclass.handleClick.call(this, o, e); 
};
