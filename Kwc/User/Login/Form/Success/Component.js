Kwf.onContentReady(function() {
    Ext.query('.kwcUserLoginFormSuccess').each(function(el) {
        if (Ext.get(el).isVisible(true) && !el.initDone) {
            el.initDone = true;
            var url = Ext.get(el).child('input.redirectTo').getValue();
            location.href = url;
        }
    }, this);
});
