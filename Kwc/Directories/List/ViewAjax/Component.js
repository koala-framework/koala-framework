Kwf.onElementReady('.kwcDirectoriesListViewAjax', function(el, config) {
    config.renderTo = el.down('.viewContainer');
    config.loadingEl = el.down('.loadingNew'); //TODO remove, create Element in JS
    config.loadingEl.enableDisplayMode();
    Kwc.Directories.List.ViewAjax.instance = new Kwc.Directories.List.ViewAjax(config);
});

Kwf.onContentReady(function(el) {
    Ext.fly(el).query('a').forEach(function(a) {
        var m = a.rel.match(/kwfViewAjaxFilter({.*?})/)
        if (m) {
            if (a.rel.kwfViewAjaxInitDone) return;
            a.rel.kwfViewAjaxInitDone = true;
            var config = Ext.decode(m[1]);
            Ext.fly(a).on('click', function(ev) {
                var view = Kwc.Directories.List.ViewAjax.byComponentId[config.viewComponentId];
                if (!view) return
                ev.stopEvent();
                view.loadViewAndPushState(document.title, a.href, {
                    filterComponentId: config.componentId
                });
                //TODO mark a as "current"

            }, this);
        }
    }, this);
});

Ext.ns('Kwc.Directories.List');
Kwc.Directories.List.ViewAjax = Ext.extend(Ext.Panel, {

    controllerUrl: null,

    directoryUrl: null, //needed to implement back-link in detail without page load

    border: false,
    layout: 'fit',
    cls: 'posts',
    initComponent: function() {
        Kwc.Directories.List.ViewAjax.byComponentId[this.componentId] = this;
        this.view = new Kwc.Directories.List.ViewAjax.View({
            controllerUrl: this.controllerUrl,
            directoryUrl: this.directoryUrl,
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

    loadViewAndPushState: function(title, href, p)
    {
        if (Kwf.Utils.HistoryState.currentState.filterComponentId != p.filterComponentId) {
            Kwf.Utils.HistoryState.currentState.filterComponentId = p.filterComponentId;
            Kwf.Utils.HistoryState.pushState(title, href);
        }
        this.loadView('home', p);
    },

    //TODO remove type parameter
    loadView: function(type, p)
    {
        this.currentMenuItem = type;

        var params = Ext.applyIf(p, {
            query: null,
            filterComponentId: null
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

Kwc.Directories.List.ViewAjax.byComponentId = {};

Kwc.Directories.List.ViewAjax.View = Ext.extend(Kwf.Binding.AbstractPanel,
{
    layout: 'fit',
    border: false,

    initComponent : function()
    {
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

        Kwf.Utils.HistoryState.on('popstate', function() {
            //TODO state should be per componentId (to support multiple view on one page)
            if (Kwf.Utils.HistoryState.currentState.viewDetail) {
                this.showDetail(Kwf.Utils.HistoryState.currentState.viewDetail);
            } else if (Kwf.Utils.HistoryState.currentState.filterComponentId) {
                this.showView();
                //TODO don't use ownerCt, instead move this whole fn up
                this.ownerCt.loadView('home', {
                    filterComponentId: Kwf.Utils.HistoryState.currentState.filterComponentId
                });
                //TODO mark filter menuitem as current
                //maybe this whole code has to be somewhere else to access the menuitem
            } else {
                this.showView();
                //TODO when going back from detail don't reload view
                //but do it if going back from filterComponentId
                this.ownerCt.loadView('home', {});
            }
        }, this);
    },

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

        if (Kwf.Utils.HistoryState.currentState.viewDetail != href) {
            Kwf.Utils.HistoryState.currentState.viewDetail = href;
            Kwf.Utils.HistoryState.pushState(document.title, href);
        }

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

                this.detailEl.query('a').forEach(function(el) {
                    if (el.href == location.protocol+'//'+location.host+this.directoryUrl) {
                        Ext.fly(el).on('click', function(ev) {
                            ev.stopEvent();
                            if (history.length > 1) {
                                history.back(); //keeps scroll position
                            } else {
                                this.showView();
                            }
                        }, this);
                    }
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

