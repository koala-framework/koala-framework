Kwf.onContentReady(function(el) {
    Ext.query('.kwcArticlesDetailFavor', el).each(function(el) {
        if (el.initDone) return;
        el.initDone = true;
        el = Ext.get(el);
        var switchContent = el.child('.switchContent');
        el.hasClass('isFavourite') ? switchContent.update(trlKwf('delete favorite')) : switchContent.update(trlKwf('save as favorite'));
        var config = Ext.decode(el.child('input').dom.value);
        el.child('a').on('click', function(ev) {
            ev.stopEvent();
            el.toggleClass('isFavourite');
            el.hasClass('isFavourite') ? switchContent.update(trlKwf('delete favorite')) : switchContent.update(trlKwf('save as favorite'));
            el.addClass('loading');
            Ext.Ajax.request({
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