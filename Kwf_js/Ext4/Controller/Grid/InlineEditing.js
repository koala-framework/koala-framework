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
        if (typeof this.gridAddButton == 'undefined') this.gridAddButton = grid.down('button#add');
        if (typeof this.gridSaveButton == 'undefined') this.gridSaveButton = grid.down('button#save');

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

        if (this.gridSaveButton) {
            this.gridSaveButton.on('click', function() {
                var s = grid.getStore();
                s.sync();
            }, this);
        }
    }
});
