var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var benchmarkBox = require('kwf/benchmark/box');

var cachedWidthEls = [];
module.exports = function getCachedWidth(e) {
    if (e.dom) e = e.dom; //ExtJS Element (hopefully)
    if (e.jquery) e = e.get(0);
    var ret = false;
    while (e) {
        if (e.getAttribute('data-width') == '100%') {
        } else if (typeof e.kwfWidthCache != 'undefined') {
            ret = e.kwfWidthCache;
            break;
        } else {
            var t = benchmarkBox.now();
            ret = e.clientWidth;
            benchmarkBox.time('getWidth uncached', benchmarkBox.now()-t);
            e.kwfWidthCache = ret;
            cachedWidthEls.push(e);
            break;
        }
        e = e.parentNode;
    }
    return ret;
};

onReady.onContentReady(function clearCachedWidth(el, options) {
    if (options.action == 'widthChange' || options.action == 'show') {
        for (var i=0; i<cachedWidthEls.length; i++) {
            var e = cachedWidthEls[i];
            if (el == e || $.contains(el, e)) {
                delete e.kwfWidthCache;
                cachedWidthEls.splice(i, 1);
                i--; //decrement because e was removed from array
            }
        }
    }
}, { defer: false, priority: -10 });
