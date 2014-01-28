Ext4.define('Kwf.Ext4.Controller.Action.Form.Delete', {
    extend: 'Ext.Action',
    constructor: function(config)
    {
        if (!config) config = {};
        config.text = trlKwf('Delete');
        config.itemId = 'delete';
        this.callParent([config]);
    }
});
