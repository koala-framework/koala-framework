Ext2.Component.prototype.disableRecursive = function() {
    this.disable();
};
Ext2.Component.prototype.enableRecursive = function() {
    this.enable();
};

Ext2.Container.prototype.disableRecursive = function() {
    if (this.items && this.items.each) {
        this.items.each(function(i) {
            i.disableRecursive();
        }, this);
    }
    Ext2.Container.superclass.disableRecursive.call(this);
};
Ext2.Container.prototype.enableRecursive = function() {
    if (this.items && this.items.each) {
        this.items.each(function(i) {
            i.enableRecursive();
        }, this);
    }
    Ext2.Container.superclass.enableRecursive.call(this);
};

//bubble sollte auch für form-felder funktionieren
Ext2.Component.prototype.bubble = Ext2.Container.prototype.bubble;
