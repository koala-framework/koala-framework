Ext.ns('Vps.Auto.GridFilter'
Vps.Auto.GridFilter.Abstract = function(config)
    Vps.Auto.GridFilter.Abstract.superclass.constructor.call(this, config
    this.addEvents('filter'
    this.toolbarItems = [
    this.id = config.i

Ext.extend(Vps.Auto.GridFilter.Abstract, Ext.util.Observable,
    reset: function()
    
    getParams: function()
        return {
    
    getToolbarItem: function()
        return this.toolbarItem
   
}
