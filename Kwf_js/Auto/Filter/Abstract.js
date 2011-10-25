Ext.ns('Kwf.Auto.Filter');
Kwf.Auto.Filter.Abstract = function(config) {
    Kwf.Auto.Filter.Abstract.superclass.constructor.call(this);
    this.addEvents('filter');
    this.toolbarItems = [];
    this.id = config.name;
    this.label = config.label || null;
};
Ext.extend(Kwf.Auto.Filter.Abstract, Ext.util.Observable, {
    reset: function() {
    },
    getParams: function() {
        return {};
    },
    getToolbarItem: function() {
        return this.toolbarItems;
    }
});
