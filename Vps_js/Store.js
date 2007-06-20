Ext.data.Store.prototype.originalLoad = Ext.data.Store.prototype.load;
Ext.override(Ext.data.Store, {
    load : function(options) {
        //wenn meta nicht gesetzt meta-parameter schicken
        if(!this.reader.meta) {
            this.baseParams.meta = true;
        }
        this.originalLoad(options);
        if (this.baseParams.meta) delete this.baseParams.meta;
        if (this.lastOptions.params && this.lastOptions.params.meta) delete this.lastOptions.params.meta;
    }
});
