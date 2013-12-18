Ext4.define('Kwf.Ext4.Menu', {
    extend: 'Ext4.Component',
    uses: 'KwfMenu',
    afterRender: function() {
        this.callParent(arguments);
        Kwf.menu = Ext.ComponentMgr.create({
            xtype: 'kwf.menu',
            renderTo: this.el.dom
        });
    }
});
