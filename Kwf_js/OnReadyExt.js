/**
 * Add a callback function that gets called once for every element that appears
 * in the dom tree
 *
 * If an input type="hidden" is found directly under the element it's value gets passed
 * as config (json decoded)
 *
 * @param element selector
 * @param callback function
 * @param options see onContentReady options, additionally checkVisibility (boolean, only call onElementReady when element is visible)
 */
Kwf.onElementReady = function(selector, fn, options) {
    if (arguments.length == 4) {
        var scope = arguments[2];
        var options = arguments[3];
        options.scope = scope;
    }
    Kwf.onJElementReady(selector, function(el, config) {
        el = Ext2.get(el[0]);
        fn.call(this, el, config);
    }, options);
};

Kwf.onElementShow = function(selector, fn,  options) {
    Kwf.onJElementShow(selector, function(el, config) {
        el = Ext2.get(el[0]);
        fn.call(this, el, config);
    }, options);
};

Kwf.onElementHide = function(selector, fn, options) {
    Kwf.onJElementHide(selector, function(el, config) {
        el = Ext2.get(el[0]);
        fn.call(this, el, config);
    }, options);
};

Kwf.onElementWidthChange = function(selector, fn, options) {
    Kwf.onJElementWidthChange(selector, function(el, config) {
        el = Ext2.get(el[0]);
        fn.call(this, el, config);
    }, options);
};
