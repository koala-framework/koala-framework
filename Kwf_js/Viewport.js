
Vps.ViewportWithoutMenu = Ext.extend(Ext.Viewport, {
    layout: 'fit',
    mabySubmit: function(cb, options) {
        var ret = true;
        this.items.each(function(i) {
            if (i.mabySubmit && !i.mabySubmit(cb, options)) {
                ret = false;
                return false; //break each
            }
        }, this);
        return ret;
    }

});

Vps.Viewport = Ext.extend(Vps.ViewportWithoutMenu, {
    initComponent: function()
    {
        Vps.menu = Ext.ComponentMgr.create({
                    xtype: 'vps.menu',
                    region: 'north',
                    height: 30
                });
        this.items.push({
            xtype: 'vps.menu',
            region: 'north',
            height: 30
        });
        this.layout = 'border';
        Vps.Viewport.superclass.initComponent.call(this);
    }
});
