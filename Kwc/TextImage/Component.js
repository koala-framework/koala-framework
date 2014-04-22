Kwf.Utils.ResponsiveEl('.kwcTextImage', [800, 420]);

//remove largeText class if >55% of element is covered by image
(function() {

var initEl = function(el) {
    var img = el.child('div.image img');
    if (img) {
        if (img.getWidth() < (el.getWidth() * 0.55)) {
            el.addClass('largeText');
        } else {
            el.removeClass('largeText');
        }
    }
};

Kwf.onContentReady(function(el) {
    Ext.get(el).select('.kwcTextImage').each(function(imageEl){
        initEl(imageEl);
    }, this);
});

Ext.fly(window).on('resize', function() {
    Ext.select('.kwcTextImage').each(function(el) {
        initEl(el);
    }, this);
});

})();
