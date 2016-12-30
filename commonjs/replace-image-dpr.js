var onReady = require('kwf/on-ready');

if (window.devicePixelRatio && window.devicePixelRatio > 1) {
    onReady.onRender('.kwfUp-kwfReplaceImageDpr2', function(el) {
        if (el.get(0).tagName.toLowerCase() == 'img') {
            el.get(0).src = el.get(0).src.replace('/images/', '/images/dpr2/');
        } else {
            var url = el.getStyle('background-image');
            url = url.replace('/images/', '/images/dpr2/');
            el.css('background-image', url);
        }
    });
}
