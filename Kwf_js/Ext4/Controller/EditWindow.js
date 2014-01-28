Ext4.define('Kwf.Ext4.Controller.EditWindow', {
    extend: 'Ext.window.Window',
    layout: 'fit',
    border: false,
    modal: true,
    closeAction: 'hide',
    stateful: true,
    bbar: ['->', {
        text: trlKwf('Save'),
        itemId: 'save'
    }, {
        text: trlKwf('Cancel'),
        itemId: 'cancel'
    }],
    initComponent: function()
    {
        this.items = [this.form];
        this.callParent(arguments);
    }
});
