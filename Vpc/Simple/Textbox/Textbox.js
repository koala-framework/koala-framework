Vps.Component.Textbox = function(componentId, componentClass, pageId) {
    Vps.Component.Textbox.superclass.constructor.call(this, componentId, componentClass, pageId);
};
Ext.extend(Vps.Component.Textbox, Vps.Component.Abstract);

Vps.Component.Textbox.prototype.handleEdit = function(o, e) {
    form = this.form;
    form.add(
        new Ext.form.TextArea({
            name: 'content',
            width:300,
            height:200,
            allowBlank:false
        })
    );
    
    form.addButton('Save', this.handleSave, this);
    form.addButton('Cancel', this.handleCancel, this);
};

