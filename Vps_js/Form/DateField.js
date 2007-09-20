Vps.Form.DateField = function(config)
{
    config = config || {};
    config = Ext.applyIf(config, {
        format: 'd.m.Y',
        altFormats : 'Y-m-d'
    });
    Vps.Form.DateField.superclass.constructor.call(this, config);
};
Ext.extend(Vps.Form.DateField, Ext.form.DateField,
{
    initComponent : function(){
        Vps.Form.DateField.superclass.initComponent.call(this);
        this.addEvents({
            menuhidden : true
        });
        if (!this.menuListeners.oldHide) {
            this.menuListeners.oldHide = this.menuListeners.hide;
            this.menuListeners.hide = function() {
                this.menuListeners.oldHide.call(this);
                this.fireEvent('menuhidden', this);
            };
        }
    }
});
Ext.reg('datefield', Vps.Form.DateField);
