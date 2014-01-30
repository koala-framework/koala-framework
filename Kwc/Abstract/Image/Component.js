if (window.devicePixelRatio && window.devicePixelRatio > 1) {
    Kwf.onElementReady('.kwcAbstractImage', function(el) {
        var dpr2src = el.dom.getAttribute('data-dpr2src');
        if (dpr2src) {
            var img = el.child('img');
            img.dom.src = dpr2src;
        }
    });
}

Kwf.onElementReady('.kwcAbstractImage', function (el) {
    var containerEl = el.child('.container');
    var baseUrl = containerEl.dom.getAttribute("data-src");

    var sizePath = baseUrl.replace('dh-{width}',
        'dh-'+containerEl.getWidth() * window.devicePixelRatio);
    containerEl.createChild({
        tag: 'img',
        src: sizePath
    });
});
