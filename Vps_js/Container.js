Ext.Component.prototype.disableRecursive = function() {
    this.disable();
};
Ext.Component.prototype.enableRecursive = function() {
    this.enable();
};

Ext.Container.prototype.disableRecursive = function() {
    this.items.each(function(i) {
        i.disableRecursive();
    }, this);
    Ext.Container.superclass.disableRecursive.call(this);
};
Ext.Container.prototype.enableRecursive = function() {
    this.items.each(function(i) {
        i.enableRecursive();
    }, this);
    Ext.Container.superclass.enableRecursive.call(this);
};

