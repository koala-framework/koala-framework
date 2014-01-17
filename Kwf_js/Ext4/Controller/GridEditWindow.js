Ext4.define('Kwf.Ext4.Controller.GridEditWindow', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        if (!this.form) {
            this.form = this.editWindow.down('form');
        }
        if (!this.windowSaveButton) this.windowSaveButton = this.editWindow.down('button#save');
        if (!this.windowCancelButton) this.windowCancelButton = this.editWindow.down('button#cancel');
        if (!this.gridAddButton) this.gridAddButton = this.grid.down('button#add');
        this.windowSaveButton.on('click', function() {
            var row = this.form.getRecord();
            this.form.updateRecord(row);
            if (row.phantom) {
                this.grid.getStore().add(row);
            }
            this.grid.getStore().sync();
            this.editWindow.hide();
        }, this);
        this.windowCancelButton.on('click', function() {
            this.editWindow.hide();
        }, this);

        this.grid.on('celldblclick', function(grid, td, cellIndex, row, tr, rowIndex, e) {
            this.form.loadRecord(row);
            this.editWindow.setTitle(trlKwf('Edit'));
            this.editWindow.show();
            this.form.down('field').focus();
        }, this);
        this.gridAddButton.on('click', function() {
            this.editWindow.setTitle(trlKwf('Add'));
            var row = this.grid.getStore().model.create();
            this.form.loadRecord(row);
            this.editWindow.show();
            this.form.down('field').focus();
        }, this);
    }
});
