E3.Component.Pic = function(componentId, componentClass) {
    E3.Component.Pic.superclass.constructor.call(this, componentId, componentClass);
};
YAHOO.lang.extend(E3.Component.Pic, E3.Component.Abstract);

E3.Component.Pic.prototype.handleSave = function() {
    var form = this.htmlelement.getElementsByTagName('form')[0];
    YAHOO.util.Connect.setForm(form, true);
    YAHOO.util.Connect.asyncRequest('POST', '/ajax/fe/save?componentId='+this.componentId+'&componentClass='+this.componentClass, 
        {success: this.handleSuccess, failure: this.handleFailure, upload: this.handleSuccess, scope: this});
};
