Ext4.define('Kwf.Ext4.Controller.Grid', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        var grid = this.grid;
        if (!this.gridDeleteButton) this.gridDeleteButton = grid.down('button#delete');
        grid.on('selectionchange', function(model, rows) {
            if (rows[0]) {
                var row = rows[0];
                if (this.gridDeleteButton) this.gridDeleteButton.enable();
            } else {
                if (this.gridDeleteButton) this.gridDeleteButton.disable();
            }
        }, this);
        if (this.gridDeleteButton) {
            this.gridDeleteButton.on('click', function() {
                var sm = grid.getSelectionModel();
                grid.getStore().remove(sm.getSelection());
                grid.getStore().sync();
            }, this);
        }
    }
});
