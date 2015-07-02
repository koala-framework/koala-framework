var responsiveEl = require('kwf/responsive-el');
var onReady = require('kwf/on-ready');

responsiveEl('.cssClass', [420]);

//remove largeText class if >55% of element is covered by image
onReady.onResize('.cssClass', function textImage(el) {
    var img = el.find('div.image .kwcAbstractImage .container');
    if (img) {
        if (img.width() < (el.width() * 0.55)) {
            el.removeClass('largeImage');
            el.addClass('largeText');
        } else {
            el.removeClass('largeText');
            el.addClass('largeImage');
        }
    }
});
