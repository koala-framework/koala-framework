// @require Kwf.Ext4.Controller.Grid
Ext4.define('Kwf.Ext4.Controller.Grid.DragDropOrder', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    autoSync: true,
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        if (!this.gridController) Ext4.Error.raise('gridController config is required');
        if (!(this.gridController instanceof Kwf.Ext4.Controller.Grid)) Ext4.Error.raise('gridController config needs to be a Kwf.Ext4.Controller.Grid');

        var plugin = this.gridController.grid.view.findPlugin('gridviewdragdrop');
        if (!plugin) Ext4.Error.raise('Didn\'t find gridviewdragdrop plugin in grid view');
        this.gridController.grid.view.on('drop', function(node, data, overRow, dropPosition, eOpts) {
            var pos = 1;
            this.gridController.grid.getStore().each(function(i) {
                if (data.records[0] == i) {
                    //skip
                    return;
                }
                if (i == overRow) {
                    data.records[0].set('pos', null);
                    if (dropPosition == 'before') {
                        data.records[0].set('pos', pos);
                    } else {
                        data.records[0].set('pos', pos+1);
                    }
                }
                pos++;
            }, this);
            if (this.autoSync) {
                this.gridController.grid.getStore().sync();
            }
        }, this);
    }
});
