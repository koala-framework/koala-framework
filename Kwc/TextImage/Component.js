Kwf.Utils.ResponsiveEl('.kwcTextImage', [420]);

//remove largeText class if >55% of element is covered by image
Kwf.onElementWidthChange('.kwcTextImage', function textImage(el) {
    var img = el.child('div.image .kwcAbstractImage .container');
    if (img) {
        if (img.getWidth() < (el.getWidth() * 0.55)) {
            el.removeClass('largeImage');
            el.addClass('largeText');
        } else {
            el.removeClass('largeText');
            el.addClass('largeImage');
        }
    }
});
