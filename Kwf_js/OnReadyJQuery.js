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
 * @param scope
 * @param options see onContentReady options, additionally checkVisibility (boolean, only call onElementReady when element is visible)
 */
Kwf.onJElementReady = function(selector, fn, scope, options) {
    Kwf._readyHandlers.push({
        selector: selector,
        fn: fn,
        scope: scope,
        options: options || {},
        num: Kwf._readyHandlers.length, //unique number, used to mark in initDone
        type: 'jquery'
    });
};
