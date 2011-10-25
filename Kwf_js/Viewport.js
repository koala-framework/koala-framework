
Kwf.ViewportWithoutMenu = Ext.extend(Ext.Viewport, {
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

Kwf.Viewport = Ext.extend(Kwf.ViewportWithoutMenu, {
    initComponent: function()
    {
        Kwf.menu = Ext.ComponentMgr.create({
                    xtype: 'kwf.menu',
                    region: 'north',
                    height: 30
                });
        this.items.push({
            xtype: 'kwf.menu',
            region: 'north',
            height: 30
        });
        this.layout = 'border';
        Kwf.Viewport.superclass.initComponent.call(this);
    }
});
