Kwf.EyeCandy.List.Plugins.ActiveChanger.Click = Ext2.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        this.list.on('childClick', function(item, ev) {
            ev.stopEvent();
            this.list.setActiveItem(item);
        }, this);
    }
});
