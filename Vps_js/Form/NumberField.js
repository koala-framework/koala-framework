Vps.Form.NumberField = function(config)
{
    config = Ext.applyIf(config, {
        decimalSeparator: ","
    });
    Vps.Form.NumberField.superclass.constructor.call(this, config);
};
Ext.extend(Vps.Form.NumberField, Ext.form.NumberField,
{
});
