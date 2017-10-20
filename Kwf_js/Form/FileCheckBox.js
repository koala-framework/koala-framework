Kwf.Form.FileCheckBox = Ext2.extend(Ext2.form.Checkbox,
{
    setValue : function(value)
    {
        if (typeof value == 'object') {
            this.setDisabled(!value.uploaded);

            var el = Ext2.get(this.name + '_show');
            if (value.url) {
                var text = '<span id="' + this.name + '_show' + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                text += '<a href="' + value.url + '" target="#blank">';
                text += '<img src=KWF_BASE_URL+"/assets/silkicons/eye.png" />';
                text += '</a>&nbsp;'+trlKwf('Show Original');
                text += '</span>';
                if (this.node) {
                    Ext2.DomHelper.overwrite(this.node, text);
                } else {
                    this.node = Ext2.DomHelper.insertAfter(this.container.dom.lastChild.lastChild, text);
                }
            } else if (el) {
                Ext2.DomHelper.overwrite(el, '');
            }
        } else {
            Kwf.Form.FileCheckBox.superclass.setValue.call(this, value);
        }
    }
});
Ext2.reg('filecheckbox', Kwf.Form.FileCheckBox);
