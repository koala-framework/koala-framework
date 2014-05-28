Kwf.onContentReady(function() {
    Ext2.query('.kwcUserFormSuccess').each(function(el) {
        window.setTimeout("window.location.href = '/'", 3000);
    }, this);
});
