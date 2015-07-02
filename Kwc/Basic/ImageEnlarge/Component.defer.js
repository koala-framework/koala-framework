var onReady = require('kwf/on-ready');

onReady.onRender('.cssClass.showHoverIcon > a', function imageEnlarge(el) {
    if (el.getWidth() > 50 && el.getHeight() > 50) {
        el.append($('<span class="outerHoverIcon"><span class="innerHoverIcon"></span></span>'));
        if (el.getWidth() < 200) {
            el.addClass('small');
        }
    }
}, { checkVisibility: true, defer: true });
