Ext.ns('Kwf.Utils');
Kwf.Utils.HistoryStateAbstract = function() {
    this.addEvents('popstate');
    this.disabled = false; //functionality can be disabled, so it behaves like a browser that doesn't support history states
    this.initialState = {};
    this.currentState = {};
};
Ext.extend(Kwf.Utils.HistoryStateAbstract, Ext.util.Observable, {
});

Kwf.Utils.HistoryStateHtml5 = function() {
    Kwf.Utils.HistoryStateHtml5.superclass.constructor.call(this);
    Ext.EventManager.on(window, 'popstate', function(event) {
        if (this.disabled) return;
        if (event.browserEvent.state) {
            this.currentState = event.browserEvent.state;
        } else {
            this.currentState = {};
        }
        this.fireEvent('popstate');
    }, this);
};
Ext.extend(Kwf.Utils.HistoryStateHtml5, Kwf.Utils.HistoryStateAbstract, {
    pushState: function(title, href) {
        if (this.disabled) return;
        window.history.pushState(this.currentState, title, href);
    },
    updateState: function() {
        if (this.disabled) return;
        window.history.replaceState(this.currentState, document.title, window.location.href);
    }
});

Kwf.Utils.HistoryStateHash = function() {
    Kwf.Utils.HistoryStateHtml5.superclass.constructor.call(this);
    this.states = {};
    if (!this.disabled) {
        //IE fallback, using # urls
        this.states[location.pathname + location.search] = {}; //initial state
        var token = Ext.History.getToken();
        if (token) {
            location.replace(token);
        }
        Ext.History.on('change', function(token) {
            if (!token) token = location.pathname + location.search;
            if (this.states[token]) {
                this.currentState = Kwf.clone(this.states[token]);
                this.fireEvent('popstate');
            }
        }, this);
        Kwf.onContentReady(function() {
            Kwf.History.init();
        }, this);
    }
};
Ext.extend(Kwf.Utils.HistoryStateHash, Kwf.Utils.HistoryStateAbstract, {
    pushState: function(title, href) {
        if (this.disabled) return;

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
        }
    },
    updateState: function() {
        if (this.disabled) return;

        var token = Ext.History.getToken();
        if (token == null) token = location.pathname + location.search;
        this.states[token] = Kwf.clone(this.currentState);
    }
});
if (window.history.pushState) {
    Kwf.Utils.HistoryState = new Kwf.Utils.HistoryStateHtml5();
} else {
    Kwf.Utils.HistoryState = new Kwf.Utils.HistoryStateHash();
}
