
Vps.Form.Hidden = Ext.extend(Ext.form.Hidden, {
    initComponent: function() {
        this.addEvents('changevalue');
        Vps.Form.Hidden.superclass.initComponent.call(this);
    },
    setValue: function(v) {
        Vps.Form.Hidden.superclass.setValue.apply(this, arguments);
        this.fireEvent('changevalue', v);
    }
});
Ext.reg('hidden', Vps.Form.Hidden);
