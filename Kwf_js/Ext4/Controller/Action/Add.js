Ext4.define('Kwf.Ext4.Controller.Action.Add', {
    extend: 'Ext.Action',
    constructor: function(config)
    {
        if (!config) config = {};
        if (!config.text) config.text = trlKwf('Add');
        if (!config.itemId) config.itemId = 'add';
        this.callParent([config]);
    }
});
