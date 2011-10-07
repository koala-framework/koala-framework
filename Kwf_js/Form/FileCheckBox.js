Kwf.Form.FileCheckBox = Ext.extend(Ext.form.Checkbox,
{
    setValue : function(value)
    {
        if (typeof value == 'object') {
            this.setDisabled(!value.uploaded);

            var el = Ext.get(this.name + '_show');
            if (value.url) {
                var text = '<span id="' + this.name + '_show' + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                text += '<a href="' + value.url + '" target="#blank">';
                text += '<img src="/assets/silkicons/eye.png" />';
                text += '</a>&nbsp;'+trlKwf('Show Original');
                text += '</span>';
                if (this.node) {
                    Ext.DomHelper.overwrite(this.node, text);
                } else {
                    this.node = Ext.DomHelper.insertAfter(this.container.dom.lastChild.lastChild, text);
                }
            } else if (el) {
                Ext.DomHelper.overwrite(el, '');
            }
        } else {
            Kwf.Form.FileCheckBox.superclass.setValue.call(this, value);
        }
    }
});
Ext.reg('filecheckbox', Kwf.Form.FileCheckBox);
