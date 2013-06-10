Kwf.Utils.ResponsiveEl('.kwcTextImage', [420]);

//remove largeText class if >70% of element is covered by image
(function() {

var initEl = function(el) {
    var img = el.child('div.image img');
    if (img) {
        if (img.getWidth() < (el.getWidth() * 0.7)) {
            el.addClass('largeText');
        } else {
            el.removeClass('largeText');
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
