Ext4.define('Kwf.Ext4.Controller.Binding.BindableToGrid', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        var grid = this.source;
        var bindable = this.bindable;
        bindable.disable();

        if (!this.saveButton && bindable.getPanel()) this.saveButton = bindable.getPanel().down('button#save');
        if (!this.deleteButton && bindable.getPanel()) this.deleteButton = bindable.getPanel().down('button#delete');
        if (!this.addButton) this.addButton = grid.down('button#add');

        if (this.saveButton) this.saveButton.disable();
        if (this.deleteButton) this.deleteButton.disable();

        grid.on('selectionchange', function(model, rows) {
            if (rows[0]) {
                var row = rows[0];
                if (bindable.getLoadedRecord() !== row) {
                    bindable.load(row);
                }
                bindable.enable();
                if (this.saveButton) this.saveButton.enable();
                if (this.deleteButton) this.deleteButton.enable();
            } else {
                bindable.disable();
                if (this.saveButton) this.saveButton.disable();
                if (this.deleteButton) this.deleteButton.disable();
            }
        }, this);

        grid.on('beforeselect', function(sm, record) {
            if (bindable.getLoadedRecord() !== record && bindable.isDirty()) {
                Ext4.Msg.show({
                    title: trlKwf('Save'),
                    msg: trlKwf('Do you want to save the changes?'),
                    buttons: Ext4.Msg.YESNOCANCEL,
                    scope: this,
                    fn: function(button) {
                        if (button == 'yes') {
                            if (this.save()) {
                                bindable.reset();
                                grid.getSelectionModel().select(record);
                            } else {
                                //validation failed re-select
                                grid.getSelectionModel().select(bindable.getLoadedRecord());
                            }
                        } else if (button == 'no') {
                            bindable.reset();
                            grid.getSelectionModel().select(record);
                        } else if (button == 'cancel') {
                            grid.getSelectionModel().select(bindable.getLoadedRecord());
                        }
                    }
                });
                return false;
            }
        }, this);

        if (this.saveButton) {
            this.saveButton.on('click', function() {
                this.save();
            }, this);
        }
        if (this.addButton) {
            this.addButton.on('click', function() {
                if (!bindable.isValid()) {
                    return false;
                }
                var s = grid.getStore();
                var row = s.model.create();
                s.add(row);
                grid.getSelectionModel().select(row);

                bindable.onAdd(row);
                this.fireEvent('add');
            }, this);
        }
        if (this.deleteButton) {
            this.deleteButton.on('click', function() {
                this.deleteSelected();
            }, this);
        }
    },

    deleteSelected: function()
    {
        Ext4.Msg.show({
            title: trlKwf('Delete'),
            msg: trlKwf('Do you really wish to remove this entry?'),
            buttons: Ext4.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    this.source.getStore().remove(bindable.getLoadedRecord());
                    this.source.getStore().sync();
                }
            }
        });
    },

    save: function()
    {
        if (!this.bindable.isValid()) {
            Ext4.Msg.alert(trlKwf('Save'),
                trlKwf("Can't save, please fill all red underlined fields correctly."));
            return false;
        }
        this.bindable.save();

        this.source.getStore().sync();

        this.fireEvent('save');

        return true;
    },

    isDirty: function()
    {
        return this.bindable.isDirty();
    },

    reset: function()
    {
        return this.bindable.reset();
    }
});
