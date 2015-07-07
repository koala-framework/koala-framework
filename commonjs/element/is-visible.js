var benchmarkBox = require('kwf/benchmark/box');

module.exports = function elementIsVisible(el) {
    if (el.dom) renderedEl = el.dom; //ExtJS Element (hopefully)
    var t = benchmarkBox.now();

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

    benchmarkBox.time('isVisible uncached', benchmarkBox.now()-t);
    return ret;
};
