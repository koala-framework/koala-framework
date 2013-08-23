/**
 * Register a function that will be called when content is loaded or shown
 * @param callback function
 * @param scope for callback
 * @param options supported are: priority (integer, higher number means it's called after all with lower number, default 0)
 */
Kwf.onJContentReady = function(fn, scope, options) {
    Kwf._readyHandlers.push({
        selector: null,
        fn: fn,
        scope: scope,
        options: options || {},
        type: 'jquery'
    });
};


if (!Kwf.isApp) {
    $(document).ready(function() {
        if (!document.body) {
            //this happens if a redirect by changing location in JS is done
            //in that case no contentReady needs to be called
            return;
        }
        Kwf.callOnContentReady(document.body, { newRender: true });
    });
}

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
