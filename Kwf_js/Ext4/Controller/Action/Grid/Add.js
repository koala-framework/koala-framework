Ext4.define('Kwf.Ext4.Controller.Action.Grid.Add', {
    extend: 'Ext.Action',
    constructor: function(config)
    {
        if (!config) config = {};
        config.text = trlKwf('Add');
        config.itemId = 'add';
        this.callParent([config]);
    }
});
