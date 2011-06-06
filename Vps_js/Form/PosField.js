Vps.Form.PosField = function(config)
{
    Vps.Form.PosField.superclass.constructor.call(this, config);
    this.on('focus', function(o, e)
        {
            this.selectText();
        },
        this
    );
};
Ext.extend(Vps.Form.PosField, Ext.form.TextField,
{
});
Ext.reg('posfield', Vps.Form.PosField);
