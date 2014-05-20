Ext4.define('Kwf.Ext4.Controller.Binding.EditWindow', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    uses: [ 'Kwf.Ext4.Data.StoreSyncQueue' ],
    focusOnEditSelector: 'field',
    form: null,
    editWindow: null,
    bindable: null,

    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        if (!this.editWindow) Ext4.Error.raise('editWindow config is required');
        if (!this.editWindow instanceof Ext4.window.Window) Ext4.Error.raise('editWindow config needs to be a Ext.window.Window');

        if (!this.saveButton) this.saveButton = this.editWindow.down('> toolbar > button#save');
        if (this.saveButton && !this.saveButton instanceof Ext4.button.Button) Ext4.Error.raise('saveButton config needs to be a Ext.button.Button');

        if (!this.deleteButton) this.deleteButton = this.editWindow.down('> toolbar > button#delete');
        if (this.deleteButton && !this.deleteButton instanceof Ext4.button.Button) Ext4.Error.raise('deleteButton config needs to be a Ext.button.Button');

        if (!this.cancelButton) this.cancelButton = this.editWindow.down('> toolbar > button#cancel');
        if (this.cancelButton && !this.cancelButton instanceof Ext4.button.Button) Ext4.Error.raise('cancelButton config needs to be a Ext.button.Button');

        if (!this.bindable) Ext4.Error.raise('bindable config is required');
        if (!this.bindable instanceof Ext4.window.Window) Ext4.Error.raise('bindable config needs to be a Kwf.Ext4.Controller.Bindable.Abstract');

        if (!this.form) {
            this.form = this.editWindow.down('form');
        }
        if (!this.form) Ext4.Error.raise('form config is required');
        if (!this.form instanceof Ext4.form.Panel) Ext4.Error.raise('form config needs to be a Ext.form.Panel');

        if (this.saveButton) {
            this.saveButton.on('click', function() {
                if (this.doSave() !== false) {
                    this.closeWindow();
                }
            }, this);
        }

        if (this.cancelButton) {
            this.cancelButton.on('click', this.onCancel, this);
        }
        this.editWindow.on('beforeclose', function() {
            this.onCancel();
            return false;
        }, this);
    },

    //store is optional, used for sync
    openEditWindow: function(row, store)
    {
        this._loadedStore = store;
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

    validateAndSubmit: function(options)
    {
        return this.bindable.validateAndSubmit(options);
    },

    doSave: function()
    {
        if (!this.bindable.isValid()) {
            Ext4.Msg.alert(trlKwf('Save'),
                trlKwf("Can't save, please fill all red underlined fields correctly."));
            return false;
        }

        this.fireEvent('beforesave');
        if (this.autoSync) {
            var syncQueue = new Kwf.Ext4.Data.StoreSyncQueue();
            syncQueue.add(this._loadedStore); //sync store first
            this.bindable.save(syncQueue);    //then bindables (so bindable grid is synced second)
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

        return true;
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
