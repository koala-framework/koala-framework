Ext4.define('Kwf.Ext4.Controller.Bindable.Grid', {
    extend: 'Kwf.Ext4.Controller.Bindable.Abstract',

    relation: null,
    gridController: null,

    load: function(row)
    {
        this._loadedRecord = row;
        var store = row[this.relation]();
        if (!store.loaded) {
            store.loaded = true;
            if (!row.phantom) {
                store.load();
            }
        }
        this.gridController.grid.bindStore(store);
    },

    reset: function()
    {
        this._loadedRecord = null;
        this.gridController.grid.unbindStore();
    },

    isDirty: function()
    {
        return false;
        if (!this.gridController.grid.getStore()) return false;
        return this.gridController.grid.getStore().getModifiedRecords().length || this.gridController.grid.getStore().getNewRecords().length;
    },

    isValid: function()
    {
        return true;
    },

    save: function(syncQueue)
    {
        if (this.gridController.grid.getStore()) {
            if (syncQueue) {
                syncQueue.add(this.gridController.grid.getStore());
            } else {
                this.gridController.grid.getStore().sync();
            }
        }
    },

    getLoadedRecord: function()
    {
        return this._loadedRecord;
    },

    enable: function()
    {
        this.gridController.grid.enable();
    },
    disable: function()
    {
        this.gridController.grid.disable();
    },
    getPanel: function()
    {
        return this.grid;
    }
});
