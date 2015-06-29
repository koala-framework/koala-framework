Ext2.ns('Kwf.Utils');
Kwf.Utils.HistoryStateAbstract = function() {
    this.addEvents('popstate');
    this.disabled = false; //functionality can be disabled, so it behaves like a browser that doesn't support history states
    this.currentState = {};
};
Ext2.extend(Kwf.Utils.HistoryStateAbstract, Ext2.util.Observable, {
    /**
     * Number of entries in the history of the current page
     **/
    entries: 0,
    pushState: Ext2.emptyFn,
    updateState: Ext2.emptyFn,
    /**
     * Replace the current state, don't use is possible as it will do a new page request in non-html5-browsers
     **/
    replaceState: Ext2.emptyFn
});

Kwf.Utils.HistoryStateHtml5 = function() {
    Kwf.Utils.HistoryStateHtml5.superclass.constructor.call(this);
    Ext2.onReady(function() {
        //in onReady to avoid getting initial popstate event that chrome sends on load
        Ext2.EventManager.on(window, 'popstate', function(event) {
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
Ext2.extend(Kwf.Utils.HistoryStateHtml5, Kwf.Utils.HistoryStateAbstract, {
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

// Fallback for <IE10
// always triggers a page load
Kwf.Utils.HistoryStateFallback = function() {
    Kwf.Utils.HistoryStateFallback.superclass.constructor.call(this);
};
Ext2.extend(Kwf.Utils.HistoryStateFallback, Kwf.Utils.HistoryStateAbstract, {
    ignoreNextChange: null,
    pushState: function(title, href) {
        location.href = href; //this will trigger a page load
    },
    updateState: function() {
    },
    replaceState: function(title, href) {
        if (this.disabled) return;
        location.replace(href); //this will trigger a page load
    }
});
if (window.history.pushState) {
    Kwf.Utils.HistoryState = new Kwf.Utils.HistoryStateHtml5();
} else {
    Kwf.Utils.HistoryState = new Kwf.Utils.HistoryStateFallback();
}
