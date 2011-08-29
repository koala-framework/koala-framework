Vps.EyeCandy.List.Plugins.StateChanger.HoverOthers = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    state: 'tiny',
    init: function() {
        this.list.on('childMouseEnter', function(item) {
            i = item.getNextSibling(); //skip one
            if (i) {
                while (i = i.getNextSibling()) {
                    i.pushState(this.state, this);
                }
            }
            i = item.getPreviousSibling(); //skip one
            if (i) {
                while (i = i.getPreviousSibling()) {
                    i.pushState(this.state, this);
                }
            }
        }, this);
        this.list.on('childMouseLeave', function(item) {
            i = item.getNextSibling(); //skip one
            if (i) {
                while (i = i.getNextSibling()) {
                    i.removeState(this.state, this);
                }
            }
            i = item.getPreviousSibling(); //skip one
            if (i) {
                while (i = i.getPreviousSibling()) {
                    i.removeState(this.state, this);
                }
            }
        }, this);
    }
});
