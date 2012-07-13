Kwf.onElementReady('.kwcDirectoriesListViewAjax', function(el, config) {
    config.renderTo = el.down('.viewContainer');
    config.loadingEl = el.down('.loadingNew'); //TODO remove, create Element in JS
    config.loadingEl.enableDisplayMode();
    Kwc.Directories.List.ViewAjax.instance = new Kwc.Directories.List.ViewAjax(config);
});

Ext.ns('Kwc.Directories.List');
Kwc.Directories.List.ViewAjax = Ext.extend(Ext.Panel, {
    border: false,
    layout: 'fit',
    cls: 'posts',
    initComponent: function() {
        this.view = new Kwc.Directories.List.ViewAjax.View({
            controllerUrl: this.controllerUrl,
            baseParams: {
                componentId: this.componentId
            }
        });
        this.items = [this.view];
        Kwc.Directories.List.ViewAjax.superclass.initComponent.call(this);
    },

    afterRender: function() {
        Kwc.Directories.List.ViewAjax.superclass.afterRender.call(this);

        this.currentMenuItem = 'home';
//         this.menuEl.select('a.home').addClass('current');
        this.loadView('home', {});

        this.onMenuItemChanged();
    },

    onMenuItemChanged: function() {
        if (this.currentMenuItem != 'home') {
            this.unreadTopArticles.hide();
            this.deliveryTimes.hide();
            this.el.up('.articlesDirectoryView').removeClass('articlesDirectoryViewHome');
        }

        this.filtersVisible = true;
//         this.filtersEl.show();

        this.blockReload = true; //for filter changevalue so they won't reload too
//         this.filter.setValue(null);
        this.blockReload = false;
    },

    loadView: function(type, p)
    {
        this.currentMenuItem = type;

        var params = Ext.applyIf(p, {
            query: null
        });
        this.view.applyBaseParams(params);
        this.onMenuItemChanged();
        this.view.load();
        this.view.showView();
    },

    showSearch: function(q)
    {
        this.menuEl.select('a').removeClass('current');
        this.loadView('search', { query: q });
    },

    showDetail: function(id) {
        this.view.showDetail(id);
    }

});


Kwc.Directories.List.ViewAjax.View = Ext.extend(Kwf.Binding.AbstractPanel,
{
    layout: 'fit',
    border: false,

    initComponent : function()
    {
        if (this.autoLoad !== false) {
            this.autoLoad = true;
        } else {
            delete this.autoLoad;
        }

        if (!this.tpl) {
            this.tpl = new Ext.XTemplate(
                '<tpl for=".">',
                    '<div class="kwfViewAjaxItem">{content}</div>',
                '</tpl>'
            );
        }
        Kwc.Directories.List.ViewAjax.View.superclass.initComponent.call(this);

        Ext.fly(window).on('scroll', function() {
            var height = this.el.getTop()+this.el.getHeight();
            height -= Ext.getBody().getViewSize().height;
            height = height - Ext.getBody().getScroll().top;
            if (height < 700) {
                this.loadMore();
            }
        }, this, { buffer: 50 });

//         Kwf.History.init();
//         Ext.History.on('change', function(token) {
//             this.onChangeHash(token);
//         }, this);
//         this.on('afterlayout', function() {
//             this.onChangeHash(Ext.History.getToken());
//         }, this);
    },

//     onChangeHash: function(token) {
//         var tokens = [];
//         if (token) tokens = token.split(':');
//         for(var i=0; i < tokens.length; ++i) {
//             var t = tokens[i];
//             if (t.substr(0, 7) == 'detail=') {
//                 t = t.substr(7);
//                 this.showDetail(t);
//                 return;
//             }
//         }
//         if (tokens.indexOf('top') != -1) {
//             this.ownerCt.menuEl.select('a').removeClass('current');
//             this.ownerCt.loadView('top', {});
//             this.ownerCt.menuEl.select('a.top').addClass('current');
// 
//         }
//         this.showView();
//     },

    loadMore: function() {
        if (!this.store || this.getStore().getCount()<20 || this.loadingMore || this.visibleDetail) return;
        this.loadingMore = true;
        this.body.addClass('loadingMore');
        this.getStore().load({
            params: {
                start: this.getStore().getCount(),
                limit: 10
            },
            add: true,
            callback: function(rows) {
                this.body.removeClass('loadingMore');
                if (rows.length) { //wenn nichts geladen sind wir bereits am ende
                    this.loadingMore = false;
                }
            },
            scope: this
        });
    },

    doAutoLoad : function()
    {
        if (!this.autoLoad) return;
        this.load();
    },
    onMetaLoad : function(result) {
        var meta = result.metaData;
        this.metaData = meta;

        if (!this.store) {
            var storeConfig = {
                proxy: new Ext.data.HttpProxy({ url: this.controllerUrl + '/json-data' }),
                reader: new Ext.data.JsonReader({
                    totalProperty: meta.totalProperty,
                    root: meta.root,
                    id: meta.id,
                    sucessProperty: meta.successProperty,
                    fields: meta.fields
                }),
                remoteSort: true,
                sortInfo: meta.sortInfo,
                pruneModifiedRecords: true
            };
            this.store = new Ext.data.Store(storeConfig);
            if (this.baseParams) {
                this.setBaseParams(this.baseParams);
                delete this.baseParams;
            }
        }

        this.store.newRecords = []; //hier werden neue records gespeichert die nicht dirty sind

        this.store.on('loadexception', function(proxy, o, response, e) {
            throw e; //re-throw
        }, this);

        var viewConfig = {
            store: this.store,
            tpl: this.tpl,
            cls: 'kwfView',
            itemSelector: 'div.kwfViewAjaxItem',
            emptyText: trlKwf('no entries found'),
            singleSelect: false,
            border: false
        };

        this.view = new Ext.DataView(viewConfig);
        if (this.visibleDetail) this.view.hide();
        this.relayEvents(this.view, ['selectionchange', 'beforeselect']);
        this.view.updateIndexes = this.view.updateIndexes.createSequence(function() {
            Kwf.callOnContentReady(this.view.el);
        }, this);

        this.view.on('click', this.onItemClick, this);

        var panel = new Ext.Panel({
            items: [ this.view],
            border: false
        });
        this.add(panel);
        this.doLayout();

        if (result.rows) {
            this.store.loadData(result);
        }
    },

    getStore : function() {
        return this.store;
    },

    //für AbstractPanel
    reset: function() {
        if (this.getStore()) {
            this.getStore().modified = [];
            this.getStore().newRecords = [];
        }
    },

    reload: function(options) {
        if (this.getStore()) {
            this.getStore().reload(options);
            this.getStore().commitChanges();
        }
    },

    load : function(params) {
        if (!this.controllerUrl) {
            throw new Error('No controllerUrl specified for AutoGrid.');
        }
        if (!params) params = {};
        if (!this.getStore()) {
            Ext.applyIf(params, Ext.apply({ meta: true }, this.baseParams));
            if (!this.metaConn) this.metaConn = new Kwf.Connection({ autoAbort: true });
            this.metaConn.request({
                url: this.controllerUrl+'/json-data',
                params: params,
                success: function(response, options, r) {
                    this.onMetaLoad(r);
                    this.ownerCt.loadingEl.hide();
                },
                scope: this
            });
        } else {
            if (!params.start) {
                params.start = 0;
            }
            this.el.mask('', 'loading');
            this.getStore().load({
                params: params,
                callback: function() {
                    this.el.unmask();
                },
                scope: this
            });
        }
    },

    getStore : function() {
        return this.store;
    },
    getBaseParams : function() {
        if (this.getStore()) {
            return this.getStore().baseParams;
        } else {
            return this.baseParams || {};
        }
    },
    setBaseParams : function(baseParams) {
        if (this.getStore()) {
            this.getStore().baseParams = baseParams;
        } else {
            //no store yet, apply them later
            this.baseParams = baseParams;
        }
    },
    applyBaseParams : function(baseParams) {
        if (this.getStore()) {
            Ext.apply(this.getStore().baseParams, baseParams);
        } else {
            //no store yet, apply them later
            if (!this.baseParams) this.baseParams = {};
            Ext.apply(this.baseParams, baseParams);
        }
    },

    onItemClick: function(view, number, target, ev) {
        var row = this.store.getAt(number);
        var target = Ext.get(ev.getTarget());
        if (target.dom.tagName.toLowerCase() != 'a') target = target.up('a');
        if (target && target.dom.rel.match(/ajaxDetail/)) {
            ev.stopEvent();
            //mehr... Link geklickt
            //this.showDetail(row.get('id'));
            this.showDetail(target.dom.href);
        }
    },

    hideDetail: function()
    {
        if (this.detailEl) {
            this.detailEl.remove();
            this.detailEl = null;
        }
    },

    showDetail: function(href)
    {
        if (this.visibleDetail == href) return;

        this.visibleDetail = href;
//         Ext.History.add('detail='+href);


        this.hideDetail();
        //this.el.up('.articlesDirectoryView').removeClass('articlesDirectoryViewHome');
        //this.ownerCt.filtersEl.hide();
        if (this.view) this.view.hide();
        this.detailEl = this.el.createChild({
            cls: 'detail loading'
        });
        var url = '/kwf/util/kwc/render';
        if (Kwf.Debug.rootFilename) url = Kwf.Debug.rootFilename + url;
        Ext.Ajax.request({
            url: url,
            params: { url: href },
            success: function(response, options) {
                if (!this.detailEl) return;
                this.detailEl.removeClass('loading');
                this.detailEl.update(response.responseText);
                var backLink = this.detailEl.createChild({
                    tag: 'a',
                    href: '#',
                    html: 'Zurück',
                    cls: 'back'
                }, this.detailEl.first());
                backLink.on('click', function(ev) {
                    ev.stopEvent();
//                     if (history.length > 1) {
//                         history.back(); //behält scroll position bei
//                     } else {
                        this.showView();
//                     }
                }, this);
                Kwf.callOnContentReady(this.detailEl);

                if (this.detailEl.child('.icons .top')) {
                    if (Articles.UnreadTopArticles.instance && Articles.UnreadTopArticles.instance.view) {
                        Articles.UnreadTopArticles.instance.view.load();
                    }
                    this.ownerCt.updateDynamicData();
                }
            },
            scope: this
        });
    },

    showView: function() {
        if (!this.visibleDetail) return;
        this.visibleDetail = null;
//         Ext.History.add('');

        this.hideDetail();
//         if (this.ownerCt.filtersVisible) {
//             this.ownerCt.filtersEl.show();
//         }
        if (this.view) this.view.show();

        this.ownerCt.onMenuItemChanged();
    }
});

