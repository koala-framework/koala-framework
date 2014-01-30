if (window.devicePixelRatio && window.devicePixelRatio > 1) {
    Kwf.onElementReady('.kwcAbstractImage', function(el) {
        var dpr2src = el.dom.getAttribute('data-dpr2src');
        if (dpr2src) {
            var img = el.child('img');
            img.dom.src = dpr2src;
        }
    });
}

Kwc._abstractImageEls = [];
Kwf.onElementReady('.kwcAbstractImage', function (el) {
    var containerEl = el.child('.container');
    var baseUrl = containerEl.dom.getAttribute("data-src");
    Kwc._abstractImageEls.push({
        el: containerEl,
        lastWidth: containerEl.getWidth(),
        baseUrl: baseUrl
    });

    var sizePath = baseUrl.replace('dh-{width}',
        'dh-'+containerEl.getWidth() * window.devicePixelRatio);
    containerEl.createChild({
        tag: 'img',
        src: sizePath
    });
});

Ext.fly(window).on('resize', function() {
    Kwc._abstractImageEls.each(function(i) {
        if (i.el.getWidth() != i.lastWidth) {
            i.lastWidth = i.el.getWidth();
            i.el.child('img').dom.src = i.baseUrl.replace('dh-{width}',
                'dh-'+i.el.getWidth() * window.devicePixelRatio);
        }
    });
}, this, {buffer: 200});
