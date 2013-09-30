/**
 * Helper function that dynamically adds eg. gt480 class to element if it's width is >480px
 *
 * Basically simulates media queries for elements
 */
Kwf.Utils.ResponsiveEl = function(selector, widths)
{
    if (!widths instanceof Array) widths = [widths];

    var initEl = function(el) {
        var changed = false;
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
                if (!el.hasClass(w.cls)) {
                    el.addClass(w.cls);
                    changed = true;
                }
            } else {
                if (el.hasClass(w.cls)) {
                    el.removeClass(w.cls);
                    changed = true;
                }
            }
        }, this);

        if (changed && !Kwf.Utils.ResponsiveEl._initialCall) {
            Kwf.callOnContentReady(el.dom, {newRender: false});
        }
    };

    Kwf.Utils.ResponsiveEl._els.push({
        selector: selector,
        fn: initEl
    });
};

Kwf.Utils.ResponsiveEl._els = [];

Kwf.onContentReady(function(el) {
    Kwf.Utils.ResponsiveEl._initialCall = true; //don't callOnContentReady on initial evaluation
    Kwf.Utils.ResponsiveEl._els.each(function(i) {
        Ext.fly(el).select(i.selector).each(i.fn);
    });
    Kwf.Utils.ResponsiveEl._initialCall = false;
}, this, {priority: -1});

Ext.fly(window).on('resize', function() {
    Kwf.Utils.ResponsiveEl._els.each(function(i) {
        Ext.select(i.selector).each(i.fn);
    });
});
