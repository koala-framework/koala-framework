Vps.Form.DateField = function(config)
{
    config = Ext.applyIf(config, {
        decimalSeparator: ","
    });
    Vps.Form.DateField.superclass.constructor.call(this, config);
};
Ext.extend(Vps.Form.DateField, Ext.form.DateField,
{
});
