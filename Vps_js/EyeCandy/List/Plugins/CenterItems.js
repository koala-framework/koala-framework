Vps.EyeCandy.List.Plugins.CenterItems = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        var listWidth = this.list.el.getWidth();
        var itemsWidth = 0;
        this.list.items.forEach(function(it) {
            itemsWidth += it.el.getWidth();
        }, this);

        var fullWidth = this.list.el.getWidth();

        var paddingLeft = (listWidth - itemsWidth) / 2;
        this.list.el.setStyle('padding-left', paddingLeft+'px');
        this.list.el.setWidth(fullWidth);
    }
});
