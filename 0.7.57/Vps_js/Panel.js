Ext.Panel.prototype.mabySubmit = function(cb, options) {
    var ret = true;
    if (this.items) {
        this.items.each(function(i) {
            if (i.mabySubmit && !i.mabySubmit(cb, options)) {
                ret = false;
                return true;
            }
        }, this);
    }
    return ret;
};
