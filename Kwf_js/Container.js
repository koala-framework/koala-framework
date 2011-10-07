Ext.Component.prototype.disableRecursive = function() {
    this.disable();
};
Ext.Component.prototype.enableRecursive = function() {
    this.enable();
};

Ext.Container.prototype.disableRecursive = function() {
    if (this.items && this.items.each) {
        this.items.each(function(i) {
            i.disableRecursive();
        }, this);
    }
    Ext.Container.superclass.disableRecursive.call(this);
};
Ext.Container.prototype.enableRecursive = function() {
    if (this.items && this.items.each) {
        this.items.each(function(i) {
            i.enableRecursive();
        }, this);
    }
    Ext.Container.superclass.enableRecursive.call(this);
};

//bubble sollte auch für form-felder funktionieren
Ext.Component.prototype.bubble = Ext.Container.prototype.bubble;
