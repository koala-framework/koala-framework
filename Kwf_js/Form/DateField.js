Kwf.Form.DateField = Ext2.extend(Ext2.form.DateField,
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
        this.on('render', function () {
            if (this.hideDatePicker) {
                this.container.child('img').setVisible(false);
            }
        }, this);
    }
});
Ext2.reg('datefield', Kwf.Form.DateField);
