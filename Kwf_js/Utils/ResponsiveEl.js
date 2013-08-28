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
            if (el.getWidth() > w) {
                el.addClass('gt'+w);
            } else {
                el.removeClass('gt'+w);
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
