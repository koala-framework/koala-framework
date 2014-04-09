Ext4.define('Kwf.Ext4.Controller.Action.ExportCsv', {
    extend: 'Ext.Action',
    constructor: function(config)
    {
        if (!config) config = {};
        if (!config.text) config.text = trlKwf('Export CSV');
        if (!config.itemId) config.itemId = 'exportCsv';
        this.callParent([config]);
    }
});
