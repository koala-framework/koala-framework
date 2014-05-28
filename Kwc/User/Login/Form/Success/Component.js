Kwf.onContentReady(function() {
    Ext2.query('.kwcUserLoginFormSuccess').each(function(el) {
        if (Ext2.get(el).isVisible(true) && !el.initDone) {
            el.initDone = true;
            var url = Ext2.get(el).child('input.redirectTo').getValue();
            location.href = url;
        }
    }, this);
});
