
Kwf.ViewportWithoutMenu = Ext2.extend(Ext2.Viewport, {
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

Kwf.Viewport = Ext2.extend(Kwf.ViewportWithoutMenu, {
    initComponent: function()
    {
        Kwf.menu = Ext2.ComponentMgr.create({
                    xtype: 'kwf.menu',
                    region: 'north',
                    height: 30
                });
        this.items.push(Kwf.menu);
        this.layout = 'border';
        Kwf.Viewport.superclass.initComponent.call(this);
    }
});
