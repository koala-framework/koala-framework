Vps.EyeCandy.List.Plugins.StateChanger.Click = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    state: 'active',
    init: function() {
        this.list.on('childClick', function(item) {
            item.pushState(this.state);
        }, this);
    }
});
