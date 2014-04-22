Ext4.define('Kwf.Ext4.Controller.EditWindow', {
    extend: 'Ext.window.Window',
    layout: 'fit',
    border: false,
    modal: true,
    closeAction: 'hide',
    stateful: true,
    showSave: true,
    showDelete: false,
    saveText: trlKwf('Save'),
    deleteText: trlKwf('Delete'),
    padding: 10,
    initComponent: function() {
        this.bbar = [];
        if (this.showDelete) {
            this.bbar.push({
                text: this.deleteText,
                itemId: 'delete'
            });
        }
        this.bbar.push('->');
        if (this.showSave) {
            this.bbar.push({
                text: this.saveText,
                itemId: 'save'
            });
        }
        this.bbar.push({
            text: this.showSave ? trlKwf('Cancel') : trlKwf('Close'),
            itemId: 'cancel'
        });
        this.callParent(arguments);
    }
});
