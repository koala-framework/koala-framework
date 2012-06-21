Kwf.onContentReady(function() {
    Ext.query('.kwcUserLoginFormSuccess').each(function(el) {
        if (el.initDone) return;
        el.initDone = true;
        var url = Ext.get(el).child('input.redirectTo').getValue();
        (function() {
            location.href = url;
        }).defer(2500);
    }, this);
});
