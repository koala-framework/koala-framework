var responsiveEl = require('kwf/commonjs/responsive-el');
var onReady = require('kwf/commonjs/on-ready');

responsiveEl('.kwcClass', [420]);

//remove largeText class if >55% of element is covered by image
onReady.onResize('.kwcClass', function textImage(el) {
    var img = el.find('.kwcBem__image .kwfUp-kwcImageContainer');
    if (img) {
        if (img.width() < (el.width() * 0.55)) {
            el.removeClass('kwcBem--largeImage');
            el.addClass('kwcBem--largeText');
        } else {
            el.removeClass('kwcBem--largeText');
            el.addClass('kwcBem--largeImage');
        }
    }
});
