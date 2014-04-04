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
    Kwf._addReadyHandler('ext', 'render', selector, fn, options);
};

Kwf.onElementShow = function(selector, fn,  options) {
    Kwf._addReadyHandler('ext', 'show', selector, fn, options);
};

Kwf.onElementHide = function(selector, fn, options) {
    Kwf._addReadyHandler('ext', 'hide', selector, fn, options);
};

Kwf.onElementWidthChange = function(selector, fn, options) {
    Kwf._addReadyHandler('ext', 'widthChange', selector, fn, options);
};
