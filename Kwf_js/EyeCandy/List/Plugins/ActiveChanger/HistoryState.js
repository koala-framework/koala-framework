Kwf.EyeCandy.List.Plugins.ActiveChanger.HistoryState = Ext2.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        this.list.on('activeChanged', function(item) {
            this._activate(item);
        }, this);

        Kwf.Utils.HistoryState.on('popstate', function() {
            var activeUrl = false;
            if (Kwf.Utils.HistoryState.currentState.eyeCandyListActive) {
                activeUrl = Kwf.Utils.HistoryState.currentState.eyeCandyListActive;
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
            if (!Kwf.Utils.HistoryState.currentState.eyeCandyListActive) {
                Kwf.Utils.HistoryState.currentState.eyeCandyListActive = href;
                Kwf.Utils.HistoryState.updateState();
            } else {
                Kwf.Utils.HistoryState.currentState.eyeCandyListActive = href;
                Kwf.Utils.HistoryState.pushState(document.title, href);
            }
        }
    }
});
