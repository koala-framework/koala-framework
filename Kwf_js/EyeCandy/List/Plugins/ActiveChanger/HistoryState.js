var kwfExtend = require('kwf/extend');
var historyState = require('kwf/history-state');

Kwf.EyeCandy.List.Plugins.ActiveChanger.HistoryState = kwfExtend(Kwf.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        this.list.on('activeChanged', function(item) {
            this._activate(item);
        }, this);

        historyState.on('popstate', function() {
            var activeUrl = false;
            if (historyState.currentState.eyeCandyListActive) {
                activeUrl = historyState.currentState.eyeCandyListActive;
            }
            if (activeUrl) {
                this.list.getItems().each(function(item) {
                    if (item.el.down('a') && item.el.down('a').dom.href == activeUrl) {
                        this.ignoreNextSetActive = true;
                        this.list.setActiveItem(item);
                    }
                }, this);
            }
        }, this);
    },
    _activate: function(item)
    {
        if (this.ignoreNextSetActive) {
            this.ignoreNextSetActive = false;
            return;
        }
        if (!this.list.el.isVisible(true)) return;
        if (item.el.down('a')) {
            var href = item.el.down('a').dom.href;
            if (!historyState.currentState.eyeCandyListActive) {
                historyState.currentState.eyeCandyListActive = href;
                historyState.updateState();
            } else {
                historyState.currentState.eyeCandyListActive = href;
                historyState.pushState(document.title, href);
            }
        }
    }
});
