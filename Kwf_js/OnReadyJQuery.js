/**
 * Only for compatibility reasons
 */
Kwf.onJContentReady = Kwf.onContentReady;

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
Kwf.onJElementReady = function(selector, fn, options) {
    if (arguments.length == 4) {
        var scope = arguments[2];
        var options = arguments[3];
        options.scope = scope;
    }
    Kwf._addReadyHandler('render', selector, fn, options);
};

Kwf.onJElementShow = function(selector, fn,  options) {
    Kwf._addReadyHandler('show', selector, fn, options);
};

Kwf.onJElementHide = function(selector, fn, options) {
    Kwf._addReadyHandler('hide', selector, fn, options);
};

Kwf.onJElementWidthChange = function(selector, fn, options) {
    Kwf._addReadyHandler('widthChange', selector, fn, options);
};
