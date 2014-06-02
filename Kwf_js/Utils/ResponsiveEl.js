/**
 * Helper function that dynamically adds eg. gt480 class to element if it's width is >480px
 *
 * Basically simulates media queries for elements
 */
Kwf.Utils.ResponsiveEl = function(selector, widths, options)
{
    var initEl;

    if (typeof(widths) != "function") {

        if (!(widths instanceof Array)) widths = [widths];
        initEl = function responsiveEl(el) {
            var changed = false;
            var elWidth = Kwf.Utils.Element.getCachedWidth(el);
            widths.each(function(w) {
                if (typeof w != 'object') {
                    w = {
                        higherWidth: w,
                        cls: 'gt'+w
                    };
                }
                var match = true;
                if (w.higherWidth && !(elWidth > w.higherWidth)) {
                    match = false;
                }
                if (w.minWidth && !(elWidth >= w.minWidth)) {
                    match = false;
                }
                if (match && w.maxWidth && !(elWidth <= w.maxWidth)) {
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
            if (changed) {
                Kwf.callOnContentReady(el.dom, { action: 'widthChange' });
            }
        };

    } else {

        initEl = widths;

    }

    Kwf.onElementWidthChange(selector, initEl, options);
};

Kwf.onContentReady(function jumpToAnchor(el) {
    if(!Kwf.Utils.ResponsiveEl._anchorDone && el === document.body) {
        Kwf.Utils.ResponsiveEl._anchorDone = true;
        if(window.location.hash) {
            var target = Ext2.get(window.location.hash.replace('#', ''));
            if (target) {
               //fix anchor target as ResponsiveEl might have changed the heights of elements
                window.scrollTo(0, target.getTop());
            }
        }
    }
}, {priority: 50});
