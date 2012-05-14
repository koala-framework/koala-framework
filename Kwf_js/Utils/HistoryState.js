Ext.ns('Kwf.Utils');
Kwf.Utils.HistoryStateCls = function() {
    this.addEvents('popstate');
    this.disabled = false; //functionality can be disabled, so it behaves like a browser that doesn't support history states
    this.initialState = {};
    this.currentState = {};
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
Ext.extend(Kwf.Utils.HistoryStateCls, Ext.util.Observable, {
    pushState: function(title, href) {
        if (window.history.pushState && !this.disabled) {
            window.history.pushState(this.currentState, title, href);
        }
    },
    replaceState: function(title, href) {
        if (window.history.replaceState && !this.disabled) {
            window.history.replaceState(this.currentState, title, href);
        }
    }
});

Kwf.Utils.HistoryState = new Kwf.Utils.HistoryStateCls;
