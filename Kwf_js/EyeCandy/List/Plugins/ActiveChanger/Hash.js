Kwf.EyeCandy.List.Plugins.ActiveChanger.Hash = Ext.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        this._initHistory();

        this.list.on('childClick', function(item, ev) {
            ev.stopEvent();
            Ext.History.add('listactiveitem'+item.listIndex);
        }, this);

        Ext.History.on('change', function(token) {
            this._setActiveItemByToken(token);
        }, this);
    },

    render: function() {
        this._setActiveItemByToken();
    },

    _setActiveItemByToken: function(token) {
        var hash = token || Ext.History.getToken();
        if (hash) {
            if (hash.substr(0, 14) == 'listactiveitem') {
                var idx = hash.substr(14);
                this.list.setActiveItem(this.list.getItem(idx));
            }
        }
    },

    _initHistory: function() {
        // whyever Ext.History.init() doesn't do this itself...
        if (!document.getElementById('history-form')) {
            var form = Ext.DomHelper.append(document.body, {
                tag: 'form', id: 'history-form', cls: 'x-history-field', children: [
                    { tag: 'input', type: 'hidden', id: 'x-history-field' },
                    { tag: 'iframe', id: 'x-history-frame' }
                ]
            });
            Ext.get(form).setDisplayed(false);
            Ext.History.init();
        }
    }
});
