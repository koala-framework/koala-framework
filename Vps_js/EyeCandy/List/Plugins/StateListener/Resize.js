Vps.EyeCandy.List.Plugins.StateListener.Resize = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    //animationConfig = {}
    //sizes = { state: {width:123, height:123}}
    init: function() {
        this.on('childStateChanged', function(item) {
            var state = item.getState();
            if (this.sizes[state]) {
                this.el.setSize(this.sizes[state]);
            }
        }, this);
    }
});
