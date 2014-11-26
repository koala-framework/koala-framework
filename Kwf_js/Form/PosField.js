Kwf.Form.PosField = function(config)
{
    Kwf.Form.PosField.superclass.constructor.call(this, config);
    this.on('focus', function(o, e)
        {
            this.selectText();
        },
        this
    );
};
Ext2.extend(Kwf.Form.PosField, Ext2.form.TextField,
{
});
Ext2.reg('posfield', Kwf.Form.PosField);
