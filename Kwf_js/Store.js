Ext2.data.Store.prototype.originalLoad = Ext2.data.Store.prototype.load;
Ext2.override(Ext2.data.Store, {
	load : function(options) {
		this.paramNames.dir = 'direction'; //hack, weil wir keinen eigenen store haben

        //wenn recordType nicht gesetzt meta-parameter schicken um ihn vom Server zu bekommen
        if(!this.recordType) {
            this.baseParams.meta = true;
        }
        var ret = this.originalLoad(options);
        this.baseParams.meta = undefined;

        return ret;
    }

});
