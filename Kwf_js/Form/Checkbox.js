Kwf.Form.Checkbox = Ext.extend(Ext.form.Checkbox,
{
    alignHelpTextIcon: function(helpEl) {
        this.helpEl.alignTo(this.getEl().up('.x-form-check-wrap-inner'), 'tr', this.helpTextOffset);
    },

    //required because clicking the fieldLabel wouldn't change the value (it changes twice actually)
    getValue: function() {
        return this.checked;
    }
});
Ext.reg('checkbox', Kwf.Form.Checkbox);
