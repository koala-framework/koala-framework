E3.Component.Textbox = function(componentId, componentClass) {
    E3.Component.Textbox.superclass.constructor.call(this, componentId, componentClass);
};
YAHOO.lang.extend(E3.Component.Textbox, E3.Component.Abstract);

E3.Component.Textbox.prototype.handleSuccess = function(o) {
    E3.Component.Textbox.superclass.handleSuccess.call(this, o);
    var resp = eval('(' + o.responseText + ')');
    for(var id in resp.createComponents) {
        var className = resp.createComponents[id].replace(/_/g, '.');
        eval('new '+className+'(\''+id+'\', \''+className+'\');');
    }
};


