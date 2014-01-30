Ext4.define('Kwf.Ext4.Controller.Grid.InlineEditing', {
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
        if (!this.gridAddButton) this.gridAddButton = grid.down('button#add');
        if (this.gridAddButton) {
            this.gridAddButton.on('click', function() {
                var s = grid.getStore();
                var row = s.model.create();
                s.add(row);
                this.fireEvent('add', row);
                grid.getSelectionModel().select(row);
                grid.getPlugin('cellediting').startEdit(row, 1);
            }, this);
        }
    }
});
