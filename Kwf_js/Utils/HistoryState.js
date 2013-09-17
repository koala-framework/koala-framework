Ext.ns('Kwf.Utils');
Kwf.Utils.HistoryStateAbstract = function() {
    this.addEvents('popstate');
    this.disabled = false; //functionality can be disabled, so it behaves like a browser that doesn't support history states
    this.currentState = {};
};
Ext.extend(Kwf.Utils.HistoryStateAbstract, Ext.util.Observable, {
    /**
     * Number of entries in the history of the current page
     **/
    entries: 0,
    pushState: Ext.emptyFn,
    updateState: Ext.emptyFn,
    /**
     * Replace the current state, don't use is possible as it will do a new page request in non-html5-browsers
     **/
    replaceState: Ext.emptyFn
});

Kwf.Utils.HistoryStateHtml5 = function() {
    Kwf.Utils.HistoryStateHtml5.superclass.constructor.call(this);
    Ext.onReady(function() {
        //in onReady to avoid getting initial popstate event that chrome sends on load
        Ext.EventManager.on(window, 'popstate', function(event) {
            if (this.disabled) return;
            this.entries--;
            if (event.browserEvent.state) {
                this.currentState = event.browserEvent.state;
            } else {
                this.currentState = {};
            }
            this.fireEvent('popstate');
        }, this);
    }, this);
};
Ext.extend(Kwf.Utils.HistoryStateHtml5, Kwf.Utils.HistoryStateAbstract, {
    pushState: function(title, href) {
        if (this.disabled) return;
        window.history.pushState(this.currentState, title, href);
        this.entries++;
    },
    updateState: function() {
        if (this.disabled) return;
        window.history.replaceState(this.currentState, document.title, window.location.href);
    },
    replaceState: function(title, href) {
        if (this.disabled) return;
        window.history.replaceState(this.currentState, title, href);
    }
});

Kwf.Utils.HistoryStateHash = function() {
    Kwf.Utils.HistoryStateHtml5.superclass.constructor.call(this);
    if (window != top) { this.disabled = true; }
    this.states = {};
    if (!this.disabled) {
        //IE fallback, using # urls
        this.states[location.pathname + location.search] = {}; //initial state
        var token = Ext.History.getToken();
        if (token && token.substr(0, 1) == '/') {
            location.replace(token);
        }
        Ext.History.on('change', function(token) {
            if (!token) token = location.pathname + location.search;
            if (token == this.ignoreNextChange) {
                //changed because we just added to history -> ignore
                this.ignoreNextChange = null;
                return;
            }
            if (this.states[token]) {
                this.currentState = Kwf.clone(this.states[token]);
                this.fireEvent('popstate');
                this.entries--;
            }
        }, this);
        Kwf.onContentReady(function() {
            Kwf.History.init();
        }, this);
    }
};
Ext.extend(Kwf.Utils.HistoryStateHash, Kwf.Utils.HistoryStateAbstract, {
    ignoreNextChange: null,
    pushState: function(title, href) {
        if (this.disabled) return;
        if (Ext.isIE6 || Ext.isIE7) {
            //don't use history state at all, simply open the new url
            location.href = href;
            return;
        }

        var prefix = location.protocol+'//'+location.host;
        if (href.substr(0, prefix.length) == prefix) {
            href = href.substr(prefix.length);
        }

        this.states[href] = Kwf.clone(this.currentState);

        var token = Ext.History.getToken();
        if (token == null) token = location.pathname + location.search;
        if (href != token) {
            if (href == location.pathname + location.search) {
                Ext.History.add('', false);
            } else {
                Ext.History.add(href, false);
            }
            this.ignoreNextChange = href;
        }
        this.entries++;
    },
    updateState: function() {
        if (this.disabled) return;

        var token = Ext.History.getToken();
        if (token == null) token = location.pathname + location.search;
        this.states[token] = Kwf.clone(this.currentState);
    },
    replaceState: function(title, href) {
        if (this.disabled) return;
        location.replace(href); //this will trigger a page load
    }
});
if (window.history.pushState) {
    Kwf.Utils.HistoryState = new Kwf.Utils.HistoryStateHtml5();
} else {
    Kwf.Utils.HistoryState = new Kwf.Utils.HistoryStateHash();
}
