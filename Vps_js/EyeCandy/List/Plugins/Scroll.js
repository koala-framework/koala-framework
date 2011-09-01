Vps.EyeCandy.List.Plugins.Scroll = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    render: function() {
        this.list.el.createChild({
            tag: 'a',
            cls: 'scrollPrevious',
            href: '#'
        }).on('click', function(ev) {
            ev.stopEvent();
        }, this);
        this.list.el.createChild({
            tag: 'a',
            cls: 'scrollNext',
            href: '#'
        }).on('click', function(ev) {
            ev.stopEvent();
        }, this);
    }
});
