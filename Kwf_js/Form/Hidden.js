
Kwf.Form.Hidden = Ext.extend(Ext.form.Hidden, {
    initComponent: function() {
        this.addEvents('changevalue');
        Kwf.Form.Hidden.superclass.initComponent.call(this);
    },
    setValue: function(v) {
        Kwf.Form.Hidden.superclass.setValue.apply(this, arguments);
        this.fireEvent('changevalue', v);
    }
});
Ext.reg('hidden', Kwf.Form.Hidden);
