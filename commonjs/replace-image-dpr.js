var onReady = require('kwf/on-ready');

if (window.devicePixelRatio && window.devicePixelRatio > 1) {
    onReady.onRender('.kwfReplaceImageDpr2', function(el) {
        if (el.get(0).tagName.toLowerCase() == 'img') {
            if (el.get(0).src.indexOf('dpr2') == -1) {
                el.get(0).src = el.get(0).src.replace('/images/', '/images/dpr2/');
            }
        } else {
            var url = el.getStyle('background-image');
            if (url.indexOf('dpr2') == -1) {
                url = url.replace('/images/', '/images/dpr2/');
            }
            el.css('background-image', url);
        }
    });
}
