var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var kwfExtend = require('kwf/extend');

var HistoryStateAbstract = function() {
    this.disabled = false; //functionality can be disabled, so it behaves like a browser that doesn't support history states
    this.currentState = {};
};
HistoryStateAbstract.prototype = {
    /**
     * Number of entries in the history of the current page
     **/
    entries: 0,
    pushState: function() {},
    updateState: function() {},
    /**
     * Replace the current state, don't use is possible as it will do a new page request in non-html5-browsers
     **/
    replaceState: function() {},

    on: function(event, cb, scope) {
        if (typeof scope != 'undefined') cb = cb.bind(scope);
        $(window).on('kwf-history-state-'+event, cb);
    }
};

var HistoryStateHtml5 = function() {
    HistoryStateHtml5.superclass.constructor.call(this);
    $(window).on('popstate', (function(event) {
        if (this.disabled) return;
        this.entries--;
        if (event.originalEvent.state) {
            this.currentState = event.originalEvent.state;
        } else {
            this.currentState = {};
        }

        //only when state cromes from "us" react on it
        //works around safari bug which fires popstate on load
        if (this.currentState['kwfUp-kwfHistoryState']) {
            $(window).trigger('kwf-history-state-popstate');
        }
    }).bind(this));

    if (!window.history.state) {
        this.updateState();
    }
};
kwfExtend(HistoryStateHtml5, HistoryStateAbstract, {
    pushState: function(title, href) {
        if (this.disabled) return;
        this.currentState['kwfUp-kwfHistoryState'] = true;
        window.history.pushState(this.currentState, title, href);
        this.entries++;
    },
    updateState: function() {
        if (this.disabled) return;
        this.currentState['kwfUp-kwfHistoryState'] = true;
        window.history.replaceState(this.currentState, document.title, window.location.href);
    },
    replaceState: function(title, href) {
        if (this.disabled) return;
        this.currentState['kwfUp-kwfHistoryState'] = true;
        window.history.replaceState(this.currentState, title, href);
    }
});

// Fallback for <IE10
// always triggers a page load
var HistoryStateFallback = kwfExtend(HistoryStateAbstract, {
    pushState: function(title, href) {
        if (this.disabled) return;
        location.href = href; //this will trigger a page load
    },
    replaceState: function(title, href) {
        if (this.disabled) return;
        location.replace(href); //this will trigger a page load
    }
});
if (window.history.pushState) {
    module.exports = new HistoryStateHtml5();
} else {
    module.exports = new HistoryStateFallback();
}
