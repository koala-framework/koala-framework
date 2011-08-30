Vps.EyeCandy.List.Plugins.StateChanger.Click = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    state: 'active',
    stateUnique: true,
    init: function() {
        this.list.on('childClick', function(item, ev) {
            if (this.stateUnique) {
                this.list.getItems().forEach(function(i) {
                    if (i!= item) item.removeState(this.state, this);
                }, this);
            }
            item.pushState(this.state, this);
            ev.stopEvent();
        }, this);
    }
});
