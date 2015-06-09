Kwf.Form.Checkbox = Ext2.extend(Ext2.form.Checkbox,
{
    alignHelpTextIcon: function(helpEl) {
        this.helpEl.alignTo(this.getEl().up('.x2-form-check-wrap-inner'), 'tr', this.helpTextOffset);
    },

    //required because clicking the fieldLabel wouldn't change the value (it changes twice actually)
    getValue: function() {
        return this.checked;
    }
});
Ext2.reg('checkbox', Kwf.Form.Checkbox);
