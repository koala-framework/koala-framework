Ext.data.Store.prototype.originalLoad = Ext.data.Store.prototype.load;
Ext.override(Ext.data.Store, {
	load : function(options) {
		this.paramNames.dir = 'direction';
        //wenn recordType nicht gesetzt meta-parameter schicken um ihn vom Server zu bekommen
        if(!this.recordType) {
            this.baseParams.meta = true;
        }
        var ret = this.originalLoad(options);
        this.baseParams.meta = undefined;

        return ret;
    }

});

//Ext.data.Store.prototype.paramNames.dir = 'direction';
