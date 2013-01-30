Kwf.onContentReady(function() {
    console.log('foo');
    Ext.query('.kwcUserFormSuccess').each(function(el) {
        console.log('bar');
        window.setTimeout("window.location.href = '/'", 3000);
    }, this);
});
