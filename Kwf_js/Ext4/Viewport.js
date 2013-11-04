Ext4.define('Kwf.Ext4.Viewport', {
    extend: 'Ext.container.Viewport',
    uses: ['Kwf.Ext4.Menu', 'Ext.layout.container.Border'],
    layout: 'border',
    initComponent : function() {
        this.items.push(Ext4.create('Kwf.Ext4.Menu', {
            region: 'north',
            height: 30,
            border: false
        }));
        this.callParent(arguments);
    }
});
