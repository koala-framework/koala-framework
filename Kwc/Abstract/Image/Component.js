Kwf.onElementReady('.kwcAbstractImage', function(el) {
    if (window.devicePixelRatio && window.devicePixelRatio > 1) {
        var dpr2src = el.dom.getAttribute('data-dpr2src');
        if (dpr2src) {
            var img = el.child('img');
            img.dom.src = dpr2src;
        }
    }
});
