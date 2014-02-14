Ext4.define('Kwf.Ext4.Controller.Binding.BindableToGrid', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    requires: [ 'Kwf.Ext4.Data.StoreSyncQueue' ],
    gridController: null,
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },


    init: function()
    {
        var grid = this.gridController.grid;
        var bindable = this.bindable;
        bindable.disable();

        if (!this.saveButton && bindable.getPanel()) this.saveButton = bindable.getPanel().down('button#save');
        if (!this.addButton) this.addButton = grid.down('button#add');

        if (this.saveButton) this.saveButton.disable();

        grid.on('selectionchange', function(model, rows) {
            if (rows[0]) {
                var row = rows[0];
                if (bindable.getLoadedRecord() !== row) {
                    bindable.load(row);
                }
                bindable.enable();
                if (this.saveButton) this.saveButton.enable();
            } else {
                bindable.disable();
                if (this.saveButton) this.saveButton.disable();
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
                var syncQueue = new Kwf.Ext4.Data.StoreSyncQueue();
                this.save(syncQueue);
                syncQueue.start({
                    success: function() {
                        this.fireEvent('savesuccess');
                    },
                    scope: this
                });

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
                this.fireEvent('add', row);

                grid.getSelectionModel().select(row);
                bindable.onAdd(row);

            }, this);
        }
    },

    save: function(syncQueue)
    {
        if (!this.bindable.isValid()) {
            Ext4.Msg.alert(trlKwf('Save'),
                trlKwf("Can't save, please fill all red underlined fields correctly."));
            return false;
        }


        if (syncQueue) {
            syncQueue.add(this.gridController.grid.getStore()); //sync this.gridController.grid store first
            this.bindable.save(syncQueue);         //then bindables (so bindable grid is synced second)
                                                   //bindable forms can still update the row as the sync is not yet started
        } else {
            this.bindable.save();                  //bindables first to allow form updating the row before sync
            this.gridController.grid.getStore().sync({
                success: function() {
                    this.fireEvent('savesuccess');
                },
                scope: this
            });
        }

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
