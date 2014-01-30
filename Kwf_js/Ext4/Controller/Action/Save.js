Ext4.define('Kwf.Ext4.Controller.Action.Save', {
    extend: 'Ext.Action',
    constructor: function(config)
    {
        if (!config) config = {};
        if (!config.text) config.text = trlKwf('Save');
        if (!config.itemId) config.itemId = 'save';
        this.callParent([config]);
    }
});
