Ext.ns('Kwf.Utils.Element');
Kwf.Utils.Element._cachedWidthEls = [];
Kwf.Utils.Element.getCachedWidth = function(el) {
    var e = el.dom;
    var ret = false;
    while (e) {
        if (e.getAttribute('data-width') == '100%') {
        } else if (typeof e.kwfWidthCache != 'undefined') {
            ret = e.kwfWidthCache;
            break;
        } else {
            var t = Kwf.Utils.BenchmarkBox.now();
            ret = Ext.fly(e).getWidth();
            Kwf.Utils.BenchmarkBox.time('getWidth uncached', Kwf.Utils.BenchmarkBox.now()-t);
            e.kwfWidthCache = ret;
            Kwf.Utils.Element._cachedWidthEls.push(e);
            break;
        }
        e = e.parentNode;
    }
    return ret;
};
Kwf.onContentReady(function(el, options) {
    if (options.action == 'widthChange' || options.action == 'show') {
        for (var i=0; i<Kwf.Utils.Element._cachedWidthEls.length; i++) {
            var e = Kwf.Utils.Element._cachedWidthEls[i];
            if ($.contains(el, e)) {
                delete e.kwfWidthCache;
                Kwf.Utils.Element._cachedWidthEls.splice(i, 1);
                i--; //decrement because e was removed from array
            }
        }
    }
});
