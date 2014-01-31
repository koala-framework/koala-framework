Ext4.define('Kwf.Ext4.Viewport', {
    extend: 'Ext.container.Viewport',
    uses: ['Kwf.Ext4.Menu', 'Ext.layout.container.Border'],
    layout: 'border',
    initComponent : function() {
        this.items[0].region = 'center';
        this.items.push(Ext4.create('Kwf.Ext4.Menu', {
            region: 'north',
            height: 35,
            border: false
        }));
        this.callParent(arguments);
    }
});
