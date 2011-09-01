Vps.EyeCandy.List.Plugins.StateChanger.HoverOthers = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    state: 'tiny',
    skipItems: 0,
    init: function() {
        this.list.on('childMouseEnter', function(item) {
            var i = item;
            for(var j=0; j<this.skipItems && i; ++j) {
                i = i.getNextSibling(); //skip
            }
            if (i) {
                while (i = i.getNextSibling()) {
                    i.pushState(this.state, this);
                }
            }

            i = item;
            for(var j=0; j<this.skipItems && i; ++j) {
                i = i.getPreviousSibling(); //skip
            }
            if (i) {
                while (i = i.getPreviousSibling()) {
                    i.pushState(this.state, this);
                }
            }
        }, this);
        this.list.on('childMouseLeave', function(item) {
            var i = item;
            for(var j=0; j<this.skipItems && i; ++j) {
                i = i.getNextSibling(); //skip
            }
            if (i) {
                while (i = i.getNextSibling()) {
                    i.removeState(this.state, this);
                }
            }

            i = item;
            for(var j=0; j<this.skipItems && i; ++j) {
                i = i.getPreviousSibling(); //skip
            }
            if (i) {
                while (i = i.getPreviousSibling()) {
                    i.removeState(this.state, this);
                }
            }
        }, this);
    }
});
