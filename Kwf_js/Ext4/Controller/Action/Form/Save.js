Ext4.define('Kwf.Ext4.Controller.Action.Form.Save', {
    extend: 'Ext.Action',
    constructor: function(config)
    {
        if (!config) config = {};
        config.text = trlKwf('Save');
        config.itemId = 'save';
        this.callParent([config]);
    }
});
