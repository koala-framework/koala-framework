Ext.data.Store.prototype.originalLoad = Ext.data.Store.prototype.loa
Ext.override(Ext.data.Store,
    load : function(options)
        //wenn recordType nicht gesetzt meta-parameter schicken um ihn vom Server zu bekomm
        if(!this.recordType)
            this.baseParams.meta = tru
       
        this.originalLoad(options
        this.baseParams.meta = undefine
   
}
