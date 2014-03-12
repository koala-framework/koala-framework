// @require Kwf.Ext4.Controller.Grid
Ext4.define('Kwf.Ext4.Controller.Grid.EditWindow', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    editWindow: null,
    form: null,
    focusOnEditSelector: 'field',
    autoSync: true,

    _addToStoreOnSave: false,
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    doSave: function()
    {
        if (!this.bindable.isValid()) {
            Ext4.Msg.alert(trlKwf('Save'),
                trlKwf("Can't save, please fill all red underlined fields correctly."));
            return false;
        }

        var row = this.bindable.getLoadedRecord();
        if (row.phantom && this._addToStoreOnSave) {
            this.gridController.grid.getStore().add(row);
        }
        if (this.autoSync) {
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
        } else {
            this.bindable.save();
        }
    },

    init: function()
    {
        if (!this.form) {
            this.form = this.editWindow.down('form');
        }
        if (!this.windowSaveButton) this.windowSaveButton = this.editWindow.down('> toolbar > button#save');
        if (!this.windowCancelButton) this.windowCancelButton = this.editWindow.down('> toolbar > button#cancel');
        if (!this.addButton) this.addButton = this.gridController.grid.down('button#add');
        if (!this.editActionColumn) this.editActionColumn = this.gridController.grid.down('actioncolumn#edit')
        if (this.windowSaveButton) {
            this.windowSaveButton.on('click', function() {
                if (this.doSave() !== false) {
                    this.closeWindow();
                }
            }, this);
        }
        if (this.windowCancelButton) {
            this.windowCancelButton.on('click', this.onCancel, this);
        }
        this.editWindow.on('beforeclose', function() {
            this.onCancel();
            return false;
        }, this);

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
                this._addToStoreOnSave = true;
            }, this);
        }
    },

    openEditWindow: function(row)
    {
        this._addToStoreOnSave = false;
        this.bindable.load(row);
        if (row.phantom) {
            this.editWindow.setTitle(trlKwf('Add'));
        } else {
            this.editWindow.setTitle(trlKwf('Edit'));
        }
        this.editWindow.show();
        if (this.focusOnEditSelector) {
            this.editWindow.down(this.focusOnEditSelector).focus();
        }
    },

    onCancel: function()
    {
        if (this.bindable.isDirty()) {
            Ext4.Msg.show({
                title: trl('Speichern'),
                msg: trl('Wollen Sie die Änderungen speichern?'),
                icon: Ext4.MessageBox.QUESTION,
                buttons: Ext4.Msg.YESNOCANCEL,
                fn: function(btn) {
                    if (btn == 'no') {
                        this.closeWindow();
                    } else if (btn == 'yes') {
                        if (this.doSave() !== false) {
                            this.closeWindow();
                        }
                    }
                },
                scope: this
            });
        } else {
            this.closeWindow();
        }
    },

    closeWindow: function()
    {
        this.bindable.reset();
        this.editWindow.hide();
    }
});
