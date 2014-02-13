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
        if (!this.windowSaveButton) this.windowSaveButton = this.editWindow.down('> toolbar > button#save');
        if (!this.windowCancelButton) this.windowCancelButton = this.editWindow.down('> toolbar > button#cancel');
        if (!this.addButton) this.addButton = this.gridController.grid.down('button#add');
        if (this.windowSaveButton) {
            this.windowSaveButton.on('click', function() {
                if (!this.bindable.isValid()) {
                    Ext4.Msg.alert(trlKwf('Save'),
                        trlKwf("Can't save, please fill all red underlined fields correctly."));
                    return false;
                }

                var row = this.bindable.getLoadedRecord();
                if (row.phantom) {
                    this.gridController.grid.getStore().add(row);
                }
                var syncQueue = new Kwf.Ext4.Data.StoreSyncQueue();
                syncQueue.add(this.gridController.grid.getStore()); //sync this.gridController.grid store first
                this.bindable.save(syncQueue);         //then bindables (so bindable grid is synced second)
                                                       //bindable forms can still update the row as the sync is not yet started
                syncQueue.start({
                    success: function() {
                        this.fireEvent('savesuccess');
                    },
                    scope: this
                });

                this.editWindow.hide();
            }, this);
        }
        if (this.windowCancelButton) {
            this.windowCancelButton.on('click', function() {
                this.editWindow.hide();
            }, this);
        }

        this.gridController.grid.on('celldblclick', function(grid, td, cellIndex, row, tr, rowIndex, e) {
            this.bindable.load(row);
            this.editWindow.setTitle(trlKwf('Edit'));
            this.editWindow.show();
            //this.form.down('field').focus();
        }, this);
        if (this.addButton) {
            this.addButton.on('click', function() {
                this.editWindow.setTitle(trlKwf('Add'));
                var row = this.gridController.grid.getStore().model.create();
                this.bindable.load(row);
                this.editWindow.show();
                //this.form.down('field').focus();
            }, this);
        }
    }
});
