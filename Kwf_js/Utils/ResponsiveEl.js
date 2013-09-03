/**
 * Helper function that dynamically adds eg. gt480 class to element if it's width is >480px
 *
 * Basically simulates media queries for elements
 */
Kwf.Utils.ResponsiveEl = function(selector, widths)
{
    if (!widths instanceof Array) widths = [widths];

    var initEl = function(el) {
        widths.each(function(w) {
            if (typeof w != 'object') {
                w = {
                    minWidth: w,
                    cls: 'gt'+w
                };
            }
            var match = true;
            if (w.minWidth && !(el.getWidth() > w.minWidth)) {
                match = false;
            }
            if (match && w.maxWidth && !(el.getWidth() < w.maxWidth)) {
                match = false;
            }
            if (match) {
                el.addClass(w.cls);
            } else {
                el.removeClass(w.cls);
            }
        }, this);
    };

    Kwf.onElementReady(selector, function(el) {
        initEl(el);
    }, this, {priority: -1});

    Ext.fly(window).on('resize', function() {
        Ext.select(selector).each(function(el) {
            initEl(el);
        }, this);
    });

};
