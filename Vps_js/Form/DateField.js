Vps.Form.DateField = Ext.extend(Ext.form.DateField,
{
    format: 'Y-m-d',
    initComponent : function(){
        Vps.Form.DateField.superclass.initComponent.call(this);
        this.addEvents('menuhidden');
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
