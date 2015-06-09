Kwf.Form.StaticField = Ext2.extend(Ext2.BoxComponent, {
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
Ext2.reg('staticfield', Kwf.Form.StaticField);
