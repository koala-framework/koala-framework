Kwf.Form.StaticField = Ext.extend(Ext.BoxComponent, {
    autoEl: {tag: 'div', cls:'kwf-form-static-field'},
    isFormField : true,
    initComponent: function() {
        Kwf.Form.StaticField.superclass.initComponent.call(this);
    },
    afterRender: function() {
        Kwf.Form.StaticField.superclass.afterRender.call(this);
        this.el.update(this.text);
    },
    getName: function() {
        return null;
    },
    getValue: function() {
        return null;
    },
    clearInvalid: function() {},
    reset: function() {},
    setValue: function() {},
    isDirty: function() { return false; },
    resetDirty: function() {},
    clearValue: function() {},
    validate: function() { return true; },
    setFormBaseParams: function() { },
    setDefaultValue: function() { }
});
Ext.reg('staticfield', Kwf.Form.StaticField);
