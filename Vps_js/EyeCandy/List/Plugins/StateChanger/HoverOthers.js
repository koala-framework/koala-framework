Vps.EyeCandy.List.Plugins.StateChanger.HoverOthers = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    state: 'tiny',
    init: function() {
        this.list.on('childMouseEnter', function(item) {
            i = item.getNextSibling(); //skip one
            if (i) {
                while (i = item.getNextSibling()) {
                    i.pushState(this.state);
                }
            }
            i = item.getPreviousSibling(); //skip one
            if (i) {
                while (i = item.getPreviousSibling()) {
                    i.pushState(this.state);
                }
            }
        }, this);
        this.list.on('childMouseLeave', function(item) {
            i = item.getNextSibling(); //skip one
            if (i) {
                while (i = item.getNextSibling()) {
                    i.popState();
                }
            }
            i = item.getPreviousSibling(); //skip one
            if (i) {
                while (i = item.getPreviousSibling()) {
                    i.popState();
                }
            }
        }, this);
    }
});
