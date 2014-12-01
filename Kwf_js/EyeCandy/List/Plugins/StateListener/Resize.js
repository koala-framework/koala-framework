Kwf.EyeCandy.List.Plugins.StateListener.Resize = Ext2.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    //animationConfig = {}
    //sizes = { state: {width:123, height:123}}
    init: function() {
        this.list.on('childStateChanged', function(item) {
            var state = item.getState();
            if (this.sizes[state]) {
                if (this.sizes[state].constructor.toString().indexOf("Array") == -1) {
                    // object, no array
                    this.sizes[state] = [ this.sizes[state] ];
                }
                // array, set multiple sizes
                this.sizes[state].forEach(function(resizeConfig) {
                    if (!resizeConfig.selector) {
                        item.el.setSize(resizeConfig, null, true);
                    } else {
                        item.el.child(resizeConfig.selector).setSize(resizeConfig, null, true);
                    }
                }, this);
            }
        }, this);
    },
    render: function() {
        this.list.items.each(function(item) {
            var state = item.getState();
            if (this.sizes[state]) {
                if (this.sizes[state].constructor.toString().indexOf("Array") == -1) {
                    // object, no array
                    this.sizes[state] = [ this.sizes[state] ];
                }
                // array, set multiple sizes
                this.sizes[state].forEach(function(resizeConfig) {
                    if (!resizeConfig.selector) {
                        item.el.setSize(resizeConfig, null, false);
                    } else {
                        item.el.child(resizeConfig.selector).setSize(resizeConfig, null, false);
                    }
                }, this);
            }
        }, this);
    }
});
