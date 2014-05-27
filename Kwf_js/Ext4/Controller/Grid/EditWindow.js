// @require Kwf.Ext4.Controller.Grid
Ext4.define('Kwf.Ext4.Controller.Grid.EditWindow', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    uses: [ 'Kwf.Ext4.Controller.Binding.EditWindow' ],

    gridController: null,
    editWindowController: null,

    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        if (this.windowSaveButton) Ext4.Error.raise('windowSaveButton config doesn\'t exist anymore');
        if (this.windowDeleteButton) Ext4.Error.raise('windowDeleteButton config doesn\'t exist anymore');
        if (this.windowCancelButton) Ext4.Error.raise('windowCancelButton config doesn\'t exist anymore');
        if (this.form) Ext4.Error.raise('form config doesn\'t exist anymore');
        if (this.editWindow) Ext4.Error.raise('editWindow config doesn\'t exist anymore');
        if (this.bindable) Ext4.Error.raise('bindable config doesn\'t exist anymore');

        if (!this.gridController) Ext4.Error.raise('gridController config is required');
        if (!(this.gridController instanceof Kwf.Ext4.Controller.Grid)) Ext4.Error.raise('gridController config needs to be a Kwf.Ext4.Controller.Grid');

        if (!this.editWindowController) Ext4.Error.raise('editWindowController config is required');
        if (!(this.editWindowController instanceof Kwf.Ext4.Controller.Binding.EditWindow)) Ext4.Error.raise('editWindowController config needs to be a Kwf.Ext4.Controller.Binding.EditWindow');

        if (!this.addButton) this.addButton = this.gridController.grid.down('button#add');
        if (this.addButton && !(this.addButton instanceof Ext4.button.Button)) Ext4.Error.raise('addButton config needs to be a Ext.button.Button');

        if (!this.editActionColumn) this.editActionColumn = this.gridController.grid.down('actioncolumn#edit')
        if (this.editActionColumn && !(this.editActionColumn instanceof Ext4.button.Button)) Ext4.Error.raise('editActionColumn config needs to be a Ext.grid.Column');

        if (this.editWindowController.deleteButton) {
            this.editWindowController.deleteButton.on('click', function() {
                this.gridController.onDeleteClick({
                    callback: function() {
                        this.editWindowController.closeWindow();
                    },
                    scope: this
                });
            }, this);
        }

        this.gridController.grid.on('celldblclick', function(grid, td, cellIndex, row, tr, rowIndex, e) {
            this.openEditWindow(row);
        }, this);

        if (this.editActionColumn) {
            this.editActionColumn.on('click', function(view, cell, rowIndex, colIndex, e) {
                this.openEditWindow(this.gridController.grid.store.getAt(rowIndex));
            }, this);
        }
        if (this.addButton) {
            this.addButton.on('click', function() {
                var row = this.gridController.grid.getStore().model.create();
                this.fireEvent('add', row);
                this.openEditWindow(row);
            }, this);
        }
    },
    openEditWindow: function(row)
    {
        this.editWindowController.openEditWindow(row, this.gridController.grid.store);
    }
});
