Vps.EyeCandy.List.Plugins.CenterItems = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    render: function() {
        var listWidth = this.list.el.getWidth();
        var itemsWidth = 0;
        var listItemWrapper = null;
        this.list.items.forEach(function(it) {
            if (it.el.isVisible()) {
                itemsWidth += it.el.getWidth();
            }
            if (!listItemWrapper) {
                listItemWrapper = it.el.parent();
            }
        }, this);

        var fullWidth = this.list.el.getWidth();
        var paddingLeft = (listWidth - itemsWidth) / 2;

        if (listItemWrapper) {
            if (this.substractOffset) paddingLeft -= this.substractOffset;
            listItemWrapper.setStyle('padding-left', paddingLeft+'px');
            this.list.el.setWidth(fullWidth);
        }
    }
});
