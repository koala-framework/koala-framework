Vps.Form.DateField = function(config)
{
    config = Ext.applyIf(config, {
        format: 'd.m.Y',
        altFormats : 'Y-m-d'
    });
    Vps.Form.DateField.superclass.constructor.call(this, config);
};
Ext.extend(Vps.Form.DateField, Ext.form.DateField,
{
});
