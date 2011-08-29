Vps.EyeCandy.List.Plugins.StateChanger.Hover = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    state: 'large',
    init: function() {
        this.list.on('childMouseEnter', function(item) {
            item.pushState(this.state);
        }, this);
        this.list.on('childMouseLeave', function(item) {
            item.popState();
        }, this);
    }
});
