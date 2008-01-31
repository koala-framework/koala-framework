Ext.Panel.prototype.mabySubmit = function() {
    var ret = true;
    if (this.items) {
        this.items.each(function(i) {
            if (i.mabySubmit && !i.mabySubmit.apply(i, arguments)) {
                ret = false;
                return true;
            }
        }, this);
    }
    return ret;
};
