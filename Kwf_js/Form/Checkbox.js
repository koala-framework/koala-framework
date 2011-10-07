Vps.Form.Checkbox = Ext.extend(Ext.form.Checkbox,
{
    alignHelpTextIcon: function(helpEl) {
        this.helpEl.alignTo(this.getEl().up('.x-form-check-wrap-inner'), 'tr', this.helpTextOffset);
    }
});
Ext.reg('checkbox', Vps.Form.Checkbox);
