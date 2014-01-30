Kwf.onElementReady('.kwcBasicImageEnlargeEnlargeTagImagePage', function (el) {
    var containerEl = el.child('.container');
    var baseUrl = containerEl.dom.getAttribute("data-src");

    var sizePath = baseUrl.replace('dh-{width}',
        'dh-{width}'+containerEl.getWidth() * window.devicePixelRatio);
    containerEl.createChild({
        tag: 'img',
        src: sizePath
    });
});
