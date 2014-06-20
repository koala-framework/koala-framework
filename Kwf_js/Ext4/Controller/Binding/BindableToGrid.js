Ext4.define('Kwf.Ext4.Controller.Binding.BindableToGrid', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    requires: [ 'Kwf.Ext4.Data.StoreSyncQueue' ],

    gridController: null,
    bindable: null,

    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },


    init: function()
    {
        if (!this.gridController) Ext4.Error.raise('gridController config is required');
        if (!(this.gridController instanceof Kwf.Ext4.Controller.Grid)) Ext4.Error.raise('gridController config needs to be a Kwf.Ext4.Controller.Grid');

        if (!this.bindable) Ext4.Error.raise('bindable config is required');
        if (!(this.bindable instanceof Kwf.Ext4.Controller.Bindable.Abstract)) Ext4.Error.raise('bindable config needs to be a Kwf.Ext4.Controller.Bindable.Abstract');

        var grid = this.gridController.grid;
        var bindable = this.bindable;
        bindable.disable();

        if (!this.saveButton && bindable.getPanel()) this.saveButton = bindable.getPanel().down('button#save');
        if (this.saveButton && !(this.saveButton instanceof Ext4.button.Button)) Ext4.Error.raise('saveButton config needs to be a Ext.button.Button');

        if (!this.addButton) this.addButton = grid.down('button#add');
        if (this.addButton && !(this.addButton instanceof Ext4.button.Button)) Ext4.Error.raise('addButton config needs to be a Ext.button.Button');

        if (this.saveButton) this.saveButton.disable();

        grid.on('selectionchange', function(model, rows) {
            if (rows[0]) {
                var row = rows[0];
                bindable.enable();
                if (bindable.getLoadedRecord() !== row) {
                    bindable.load(row);
                }
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

        this.gridController.on('bindstore', this.onBindStore, this);
        if (grid.getStore()) this.onBindStore(grid.getStore());

        if (this.saveButton) {
            this.saveButton.on('click', function() {
                var syncQueue = new Kwf.Ext4.Data.StoreSyncQueue();
                this.save(syncQueue);
                syncQueue.start();

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

    onRefreshStore: function()
    {
        var curr = this.bindable.getLoadedRecord();
        if (curr) {
            var newRow = this.gridController.grid.getStore().getById(curr.getId());
            var selected = this.gridController.grid.getSelectionModel().isSelected(newRow);
            //A refresh that loads the current row, a new object is created.
            //Load the new row, dirty values should be kept by the bindable
            if (newRow && newRow !== curr && selected) {
                this.bindable.enable();
                this.bindable.load(newRow);
            }
        }
    },

    onBindStore: function(store)
    {
        store.on('refresh', this.onRefreshStore, this);
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
            syncQueue.on('finished', function(syncQueue) {
                if (!syncQueue.hasException) {
                    this.fireEvent('savesuccess');
                    var rec = this.bindable.getLoadedRecord();
                    if (rec) this.bindable.load(rec);
                }
            }, this, { single: true });
        } else {
            this.bindable.save();                  //bindables first to allow form updating the row before sync
            this.gridController.grid.getStore().sync({
                success: function() {
                    this.fireEvent('savesuccess');
                    var rec = this.bindable.getLoadedRecord();
                    if (rec) this.bindable.load(rec);
                },
                scope: this
            });
        }

        this.fireEvent('save');

        return true;
    },

    isValid: function()
    {
        return this.bindable.isValid();
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
