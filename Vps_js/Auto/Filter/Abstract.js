Ext.ns('Vps.Auto.Filter');
Vps.Auto.Filter.Abstract = function(config) {
    Vps.Auto.Filter.Abstract.superclass.constructor.call(this);
    this.addEvents('filter');
    this.toolbarItems = [];
    this.id = config.name;
    this.label = config.label || null;
};
Ext.extend(Vps.Auto.Filter.Abstract, Ext.util.Observable, {
    reset: function() {
    },
    getParams: function() {
        return {};
    },
    getToolbarItem: function() {
        return this.toolbarItems;
    }
});
