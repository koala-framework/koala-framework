Ext4.define('Kwf.Ext4.Controller.Bindable.Grid', {
    extend: 'Kwf.Ext4.Controller.Bindable.Abstract',

    relation: null,
    grid: null,

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
        this.grid.bindStore(store);
    },

    reset: function()
    {
        this._loadedRecord = null;
        this.grid.unbindStore();
    },

    isDirty: function()
    {
        return false;
        if (!this.grid.getStore()) return false;
        return this.grid.getStore().getModifiedRecords().length || this.grid.getStore().getNewRecords().length;
    },

    isValid: function()
    {
        return true;
    },

    save: function()
    {
        this.grid.getStore().sync();
    },

    getLoadedRecord: function()
    {
        return this._loadedRecord;
    },

    enable: function()
    {
        this.grid.enable();
    },
    disable: function()
    {
        this.grid.disable();
    },
    getPanel: function()
    {
        return this.grid;
    }
});
