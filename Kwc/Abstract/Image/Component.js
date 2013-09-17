if (window.devicePixelRatio && window.devicePixelRatio > 1) {
    Kwf.onElementReady('.kwcAbstractImage', function(el) {
        var dpr2src = el.dom.getAttribute('data-dpr2src');
        if (dpr2src) {
            var img = el.child('img');
            img.dom.src = dpr2src;
        }
    });
}

//resize image to 100% container width if image is larger than container
(function() {

var initEl = function(el) {
    if (el.child('img') && el.getWidth()) {
        if (el.getWidth() < (el.child('img').dom.getAttribute('width')) / window.devicePixelRatio) {
            el.addClass('autoWidth');
        } else {
            el.removeClass('autoWidth');
        }
    }
};

Kwf.onElementReady('.kwcAbstractImage', function(el) {
    initEl(el);
});

Ext.fly(window).on('resize', function() {
    Ext.select('.kwcAbstractImage').each(function(el) {
        initEl(el);
    }, this);
});

})();
