Kwf.EyeCandy.List.Plugins.StateListener.ItemClass = Ext2.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        this.list.on('childStateChanged', this._change, this);
    },
    _change: function(item) {
        this.list.states.forEach(function(s) {
            item.el.removeClass('itemState'+s.substr(0, 1).toUpperCase()+s.substr(1));
        }, this);
        var s = item.getState();
        item.el.addClass('itemState'+s.substr(0, 1).toUpperCase()+s.substr(1));
    }
});