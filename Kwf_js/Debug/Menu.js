Ext.onReady(function() {
    if (Kwf.Debug.showMenu) {
        Kwf.Debug.requestsStore = new Ext.data.SimpleStore({
            fields: [
                {name: 'time'},
                {name: 'url', type: 'string'},
                {name: 'params', type: 'string'},
                {name: 'requestNum', type: 'string'},
                {name: 'queries', type: 'int'},
                {name: 'rows', type: 'int'},
                {name: 'explainRows', type: 'int'}
            ]
        });
        var data = [[new Date(), location.pathname, '', Kwf.Debug.requestNum]];
        Kwf.Debug.requestsStore.loadData(data);
    }
    if (!Kwf.isApp && Kwf.Debug.showMenu) {
        new Ext.Button({
            icon: '/assets/silkicons/bug.png',
            cls: 'x-btn-icon',
            tooltip: 'Debug-Menü',
            style: 'position: absolute; right: 0; top: 0',
            menu: new Kwf.Debug.Menu(),
            renderTo: Ext.getBody()
        });
    }
});

Kwf.Debug.Menu = function(config) {
    Kwf.Debug.Menu.superclass.constructor.call(this, config);
    if (Kwf.Debug.querylog) {
        this.add({
            icon: '/assets/silkicons/database.png',
            cls: 'x-btn-text-icon',
            text: 'SQL-Debug',
            handler: function() {
                var requests = new Kwf.Debug.Requests({
                    region: 'north',
                    height: 200,
                    store: Kwf.Debug.requestsStore,
                    split: true
                });
                var queries = new Kwf.Debug.SqlQueries({
                    region: 'center'
                });
                requests.getSelectionModel().on('rowselect', function(model, rowIndex, r) {
                    queries.load(r.get('requestNum'));
                }, this);
                var win = new Ext.Window({
                    title: 'SQL-Debug',
                    layout: 'border',
                    items: [requests, queries],
                    width: 800,
                    height: 600
                });
                win.show();
                requests.loadQueryCount();
            },
            scope: this
        });
    }

    this.add({
        text: 'Clear Assets-cache',
        scope: this,
        handler: function() {
            Ext.Ajax.request({
                url: '/kwf/debug/assets/json-clear-assets-cache',
                success: function() {
                    Ext.Msg.alert('Assets Cache', 'Successfully cleared');
                }
            });
        }
    });
    var chk = Kwf.Debug.autoClearCache;
    this.add(new Ext.menu.CheckItem({
        text: 'Auto-Clear Assets Cache',
        scope: this,
        handler: function(menu) {
            Ext.Ajax.request({
                url: '/kwf/debug/assets/json-set-debug-assets',
                params: { 'autoClearCache' : !menu.checked+0 }
            });
        },
        checked: chk
    }));
    chk = Kwf.Debug.js;
    this.add(new Ext.menu.CheckItem({
        text: '.js - Debug Assets',
        scope: this,
        handler: function(menu) {
            Ext.Ajax.request({
                url: '/kwf/debug/assets/json-set-debug-assets',
                params: { 'js' : !menu.checked+0 }
            });
        },
        checked: chk
    }));
    chk = Kwf.Debug.css;
    this.add(new Ext.menu.CheckItem({
        text: '.css - Debug Assets',
        scope: this,
        handler: function(menu) {
            Ext.Ajax.request({
                url: '/kwf/debug/assets/json-set-debug-assets',
                params: { 'css' : !menu.checked+0 }
            });
        },
        checked: chk
    }));
    this.add({
        text: 'Components Tree',
        icon: '/assets/silkicons/application_side_tree.png',
        cls: 'x-btn-text-icon',
        scope: this,
        handler: function() {
            var win = new Ext.Window({
                title: 'Components Tree',
                layout: 'fit',
                items: [new Kwf.Auto.TreePanel({
                    controllerUrl: '/kwf/debug/tree-cache'
                })],
                width: 800,
                height: 600
            });
            win.show();
        }
    });
    this.add({
        text: 'show debug console',
        icon: '/assets/silkicons/bug.png',
        cls: 'x-btn-text-icon',
        scope: this,
        handler: function() {
            Ext.log();
        }
    });
    this.add({
        text: 'stop debugging',
        icon: '/assets/silkicons/stop.png',
        cls: 'x-btn-text-icon',
        scope: this,
        handler: function() {
            Ext.Ajax.request({
                url: '/kwf/debug/activate/json-deactivate',
                success: function() {
                    location.href = location.href;
                }
            });
        }
    });
};


Ext.extend(Kwf.Debug.Menu, Ext.menu.Menu, {
});

