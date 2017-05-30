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
        $(window).on('kwfUp-kwf-history-state-'+event, cb);
    }
};

var HistoryStateHtml5 = function() {
    HistoryStateHtml5.superclass.constructor.call(this);
    $(window).on('popstate', (function(event) {
        if (this.disabled) return;
        this.entries--;
        if (event.originalEvent.state && event.originalEvent.state['kwfUp-history']) {
            this.currentState = event.originalEvent.state['kwfUp-history'];
        } else {
            this.currentState = {};
        }

        //only when state cromes from "us" react on it
        //works around safari bug which fires popstate on load
        if (this.currentState['kwfHistoryState']) {
            $(window).trigger('kwfUp-kwf-history-state-popstate');
        }
    }).bind(this));

    if (!window.history.state || !window.history.state['kwfUp-history'] || !window.history.state['kwfUp-history']['kwfHistoryState']) {
        this.updateState();
    }
};
kwfExtend(HistoryStateHtml5, HistoryStateAbstract, {
    _getCurrentState: function() {
        if (this.disabled) return;
        this.currentState['kwfHistoryState'] = true;
        var state = window.history.state;
        if (!state) state = {};
        if (!state['kwfUp-history']) state['kwfUp-history'] = {};
        for (var attr in this.currentState) {
            state['kwfUp-history'][attr] = this.currentState[attr];
        }
        return state;
    },
    pushState: function(title, href) {
        var state = this._getCurrentState();
        if (state) {
            window.history.pushState(state, title, href);
            this.entries++;
        }
    },
    updateState: function() {
        var state = this._getCurrentState();
        if (state) {
            window.history.replaceState(state, document.title, window.location.href);
        }
    },
    replaceState: function(title, href) {
        var state = this._getCurrentState();
        if (state) {
            window.history.replaceState(state, title, href);
        }
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
