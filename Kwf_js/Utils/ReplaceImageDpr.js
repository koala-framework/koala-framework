if (window.devicePixelRatio && window.devicePixelRatio > 1) {
    Kwf.onElementReady('.kwfReplaceImageDpr2', function(el) {
        if (el.dom.tagName.toLowerCase() == 'img') {
            el.dom.src = el.dom.src.replace('/images/', '/images/dpr2/');
        } else {
            var url = el.getStyle('background-image');
            url = url.replace('/images/', '/images/dpr2/');
            el.setStyle('background-image', url);
        }
    });
}
