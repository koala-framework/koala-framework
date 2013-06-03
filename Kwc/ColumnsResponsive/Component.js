(function() {

    var initEl = function(el) {
        if (el.getWidth() > 480) {
            el.addClass('gt480');
        } else {
            el.removeClass('gt480');
        }
    };

    Kwf.onElementReady('.kwcColumnsResponsive', function(el) {
        initEl(el);
    });

    Ext.fly(window).on('resize', function() {
        Ext.select('.kwcColumnsResponsive').each(function(el) {
            initEl(el);
        }, this);
    });


})();
