// @require Kwf.Ext4.Controller.Grid
Ext4.define('Kwf.Ext4.Controller.Grid.EditWindow', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    focusOnEditSelector: 'field',
    autoSync: true,

    form: null,
    editWindow: null,
    bindable: null,
    gridController: null,

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
        this.fireEvent('save');
    },

    init: function()
    {
        if (!this.gridController) Ext4.Error.raise('gridController config is required');
        if (!this.gridController instanceof Ext4.window.Window) Ext4.Error.raise('gridController config needs to be a Kwf.Ext4.Controller.Grid');
        if (!this.editWindow) Ext4.Error.raise('editWindow config is required');
        if (!this.editWindow instanceof Ext4.window.Window) Ext4.Error.raise('editWindow config needs to be a Ext.window.Window');
        if (!this.bindable) Ext4.Error.raise('bindable config is required');
        if (!this.bindable instanceof Ext4.window.Window) Ext4.Error.raise('bindable config needs to be a Kwf.Ext4.Controller.Bindable.Abstract');

        if (!this.form) {
            this.form = this.editWindow.down('form');
        }
        if (!this.form) Ext4.Error.raise('form config is required');
        if (!this.form instanceof Ext4.form.Panel) Ext4.Error.raise('form config needs to be a Ext.form.Panel');

        if (!this.windowSaveButton) this.windowSaveButton = this.editWindow.down('> toolbar > button#save');
        if (this.windowSaveButton && !this.windowSaveButton instanceof Ext4.button.Button) Ext4.Error.raise('windowSaveButton config needs to be a Ext.button.Button');

        if (!this.windowCancelButton) this.windowCancelButton = this.editWindow.down('> toolbar > button#cancel');
        if (this.windowCancelButton && !this.windowCancelButton instanceof Ext4.button.Button) Ext4.Error.raise('windowCancelButton config needs to be a Ext.button.Button');

        if (!this.addButton) this.addButton = this.gridController.grid.down('button#add');
        if (this.addButton && !this.addButton instanceof Ext4.button.Button) Ext4.Error.raise('addButton config needs to be a Ext.button.Button');

        if (!this.editActionColumn) this.editActionColumn = this.gridController.grid.down('actioncolumn#edit')
        if (this.editActionColumn && !this.editActionColumn instanceof Ext4.button.Button) Ext4.Error.raise('editActionColumn config needs to be a Ext.grid.Column');

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
