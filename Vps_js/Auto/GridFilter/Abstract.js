Ext.ns('Vps.Auto.GridFilter');
Vps.Auto.GridFilter.Abstract = function(config) {
    Vps.Auto.GridFilter.Abstract.superclass.constructor.call(this);
    this.addEvents('filter');
    this.toolbarItems = [];
    this.id = config.id;
};
Ext.extend(Vps.Auto.GridFilter.Abstract, Ext.util.Observable, {
    reset: function() {
    },
    getParams: function() {
        return {};
    },
    getToolbarItem: function() {
        return this.toolbarItems;
    }
});
