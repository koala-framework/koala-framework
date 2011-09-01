Vps.EyeCandy.List.Plugins.StateListener.Resize = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    //animationConfig = {}
    //sizes = { state: {width:123, height:123}}
    init: function() {
        this.list.on('childStateChanged', function(item) {
            var state = item.getState();
            if (this.sizes[state]) {
                item.el.setSize(this.sizes[state], null, true);
            }
        }, this);
    },
    render: function() {
        this.list.items.each(function(item) {
            var state = item.getState();
            if (this.sizes[state]) {
                item.el.setSize(this.sizes[state], null, false); //set initial size
            }
        }, this);
    }
});
