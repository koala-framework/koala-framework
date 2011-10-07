Kwf.Form.DateField = Ext.extend(Ext.form.DateField,
{
    format: trlKwf('Y-m-d'),
    width: 90,
    initComponent : function(){
        Kwf.Form.DateField.superclass.initComponent.call(this);
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
Ext.reg('datefield', Kwf.Form.DateField);
