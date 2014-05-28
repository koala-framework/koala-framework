Kwf.onContentReady(function(el) {
    Ext2.query('.kwcFavourites', el).each(function(el) {
        if (el.initDone) return;
        el.initDone = true;
        el = Ext2.get(el);
        var switchContent = el.child('.switchContent');
        var config = Ext2.decode(el.child('input').dom.value);
        el.hasClass('isFavourite') ? switchContent.update(config.deleteFavourite) : switchContent.update(config.saveFavourite);
        el.child('a').on('click', function(ev) {
            ev.stopEvent();
            el.toggleClass('isFavourite');
            el.hasClass('isFavourite') ? switchContent.update(config.deleteFavourite) : switchContent.update(config.saveFavourite);
            el.addClass('loading');
            Ext2.Ajax.request({
                url: config.controllerUrl+'/json-favourite',
                params: {
                    componentId: config.componentId,
                    is_favourite: el.hasClass('isFavourite') ? 1 : 0
                },
                callback: function() {
                    el.removeClass('loading');
                },
                success: function() {
                    var count = 0;
                    el.hasClass('isFavourite') ? count += 1 : count -= 1;
                    Kwf.fireComponentEvent('favouritesChanged', count);
                },
                failure: function() {
                    //toggle back
                    el.toggleClass('isFavourite');
                },
                scope: this
            });
        }, this);
    }, this);
});
