if (!Kwf.Utils) Kwf.Utils = {};
if (!Kwf.Utils.Element) Kwf.Utils.Element = {};

Kwf.Utils.Element._cachedWidthEls = [];
Kwf.Utils.Element.getCachedWidth = function(e) {
    if (e.dom) e = e.dom; //ExtJS Element (hopefully)
    if (e instanceof $) e = e.get(0);
    var ret = false;
    while (e) {
        if (e.getAttribute('data-width') == '100%') {
        } else if (typeof e.kwfWidthCache != 'undefined') {
            ret = e.kwfWidthCache;
            break;
        } else {
            var t = Kwf.Utils.BenchmarkBox.now();
            ret = e.clientWidth;
            Kwf.Utils.BenchmarkBox.time('getWidth uncached', Kwf.Utils.BenchmarkBox.now()-t);
            e.kwfWidthCache = ret;
            Kwf.Utils.Element._cachedWidthEls.push(e);
            break;
        }
        e = e.parentNode;
    }
    return ret;
};
Kwf.Utils.Element.isVisible = function elementIsVisible(el) {
    if (el.dom) renderedEl = el.dom; //ExtJS Element (hopefully)
    var t = Kwf.Utils.BenchmarkBox.now();

    /* variant 1: Ext2: has dependency on ext2
    //var ret = Ext2.fly(el).isVisible();
    */

    /* variant 2: jquery: not always correct
    //var ret = $(el).is(':visible');
    */

    /* variant 3: manuallycheck visiblity+display, basically what ext2 does */
    var ret = true;
    while (el && el.tagName && el.tagName.toLowerCase() != "body") {
        var vis = !($(el).css('visibility') == 'hidden' || $(el).css('display') == 'none');
        if (!vis) {
            ret = false;
            break;
        }
        el = el.parentNode;
    }

    Kwf.Utils.BenchmarkBox.time('isVisible uncached', Kwf.Utils.BenchmarkBox.now()-t);
    return ret;
};

Kwf.onContentReady(function clearCachedWidth(el, options) {
    if (options.action == 'widthChange' || options.action == 'show') {
        for (var i=0; i<Kwf.Utils.Element._cachedWidthEls.length; i++) {
            var e = Kwf.Utils.Element._cachedWidthEls[i];
            if (el == e || $.contains(el, e)) {
                delete e.kwfWidthCache;
                Kwf.Utils.Element._cachedWidthEls.splice(i, 1);
                i--; //decrement because e was removed from array
            }
        }
    }
}, { defer: false, priority: -10 });
