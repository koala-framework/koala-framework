var onReadyJquery = require('kwf/on-ready');

module.exports = {

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
    onRender: function(selector, fn, options) {
        if (arguments.length == 4) {
            var scope = arguments[2];
            var options = arguments[3];
            options.scope = scope;
        }
        onReadyJquery.onRender(selector, function(el, config) {
            el = Ext2.get(el[0]);
            fn.call(this, el, config);
        }, options);
    },

    onShow: function(selector, fn,  options) {
        onReadyJquery.onShow(selector, function(el, config) {
            el = Ext2.get(el[0]);
            fn.call(this, el, config);
        }, options);
    },

    onHide: function(selector, fn, options) {
        onReadyJquery.onHide(selector, function(el, config) {
            el = Ext2.get(el[0]);
            fn.call(this, el, config);
        }, options);
    },

    onResize: function(selector, fn, options) {
        onReadyJquery.onResize(selector, function(el, config) {
            el = Ext2.get(el[0]);
            fn.call(this, el, config);
        }, options);
    }

};
