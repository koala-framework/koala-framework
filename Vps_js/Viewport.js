Vps.Viewport = Ext.extend(Ext.Viewport, {
    initComponent: function()
    {
        Vps.menu = new Vps.Menu.Index({
                    region: 'north',
                    height: 30
                })
        this.items.push(Vps.menu);
        this.layout = 'border';
        Vps.Viewport.superclass.initComponent.call(this);
    },
    mabySubmit: function(options) {
        var ret = true;
        this.items.each(function(i) {
            if (i.mabySubmit && !i.mabySubmit(options)) {
                ret = false;
                return false; //break each
            }
        }, this);
        return ret;
    }
});
