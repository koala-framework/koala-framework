Ext4.define('Kwf.Ext4.Controller.Bindable.Grid', {
    extend: 'Kwf.Ext4.Controller.Bindable.Abstract',

    relation: null,
    gridController: null,

    load: function(row)
    {
        var storeName = this.relation+'Store'; //same naming as in Ext.data.association.HasMany
        if (this._loadedRecord && this._loadedRecord[storeName]) {
            //if new row has same id as currently loaded copy the store
            //this makes sures dirty values are kept
            if (row.getId() == this._loadedRecord.getId()) {
                row[storeName] = this._loadedRecord[storeName];
            }
        }
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
