var $ = require('jquery');
var onReady = require('kwf/commonjs/on-ready');

onReady.onRender('.kwcClass.showHoverIcon > a', function imageEnlarge(el) {
    if (el.width() > 50 && el.height() > 50) {
        el.append($('<span class="outerHoverIcon"><span class="innerHoverIcon"></span></span>'));
        if (el.width() < 200) {
            el.addClass('small');
        }
    }
}, { checkVisibility: true, defer: true });
