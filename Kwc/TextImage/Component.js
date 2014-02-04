Kwf.Utils.ResponsiveEl('.kwcTextImage', [800, 420, {maxWidth: 420, cls: 'lt420'}]);

//remove largeText class if >55% of element is covered by image
(function() {

var initEl = function(el) {
    var img = el.child('div.image img');
    if (img) {
        if (img.getWidth() < (el.getWidth() * 0.55)) {
            el.removeClass('largeImage');
            el.addClass('largeText');
        } else {
            el.removeClass('largeText');
            el.addClass('largeImage');
        }
    }
};

Kwf.onElementReady('.kwcTextImage', function(el) {
    initEl(el);
});

Ext.fly(window).on('resize', function() {
    Ext.select('.kwcTextImage').each(function(el) {
        initEl(el);
    }, this);
});

})();
