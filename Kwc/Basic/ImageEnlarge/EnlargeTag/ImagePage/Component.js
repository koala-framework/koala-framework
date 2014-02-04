Kwf.onElementReady('.kwcBasicImageEnlargeEnlargeTagImagePage', function (el) {
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
