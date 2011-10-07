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
Ext.extend(Kwf.Form.PosField, Ext.form.TextField,
{
});
Ext.reg('posfield', Kwf.Form.PosField);
