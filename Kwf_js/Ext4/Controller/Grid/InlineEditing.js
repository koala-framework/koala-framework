// @require Kwf.Ext4.Controller.Grid
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
        if (!this.grid) Ext4.Error.raise('grid config is required');
        if (!this.grid instanceof Ext4.grid.Panel) Ext4.Error.raise('grid config needs to be a Ext.grid.Panel');

        var grid = this.grid;
        if (typeof this.gridAddButton == 'undefined') this.gridAddButton = grid.down('button#add');
        if (this.gridAddButton && !this.gridAddButton instanceof Ext4.button.Button) Ext4.Error.raise('gridAddButton config needs to be a Ext.button.Button');
        if (typeof this.gridSaveButton == 'undefined') this.gridSaveButton = grid.down('button#save');
        if (this.gridSaveButton && !this.gridSaveButton instanceof Ext4.button.Button) Ext4.Error.raise('gridSaveButton config needs to be a Ext.button.Button');

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
