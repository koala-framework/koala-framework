Kwf.onContentReady(function(el) {
    Ext.fly(el).query('a').forEach(function(a) {
        var m = a.rel.match(/kwfViewAjaxFilter({.*?})/)
        if (m) {
            if (a.kwfViewAjaxInitDone) return;
            a.kwfViewAjaxInitDone = true;
            var config = Ext.decode(m[1]);

            if (!Kwc.Directories.List.ViewAjax.filterLinks[config.viewComponentId]) Kwc.Directories.List.ViewAjax.filterLinks[config.viewComponentId] = [];
            Kwc.Directories.List.ViewAjax.filterLinks[config.viewComponentId].push(a);

            Ext.fly(a).on('click', function(ev) {
                var view = Kwc.Directories.List.ViewAjax.byDirectoryViewComponentId[this.config.viewComponentId];
                if (!view) return
                view.loadView({
                    filterComponentId: this.config.componentId
                });
                if (view._getState().viewFilter != this.config.componentId) {
                    view._getState().viewFilter = this.config.componentId;
                    view._getState().menuLinkId = a.id;
                    Kwf.Utils.HistoryState.pushState(document.title, a.href);
                }
                Kwc.Directories.List.ViewAjax.filterLinks[this.config.viewComponentId].forEach(function(i) {
                    Ext.fly(i).removeClass('current');
                }, this);
                Ext.fly(a).addClass('current');

                ev.stopEvent();

            }, {config: config});
        }
    }, this);
});

Kwf.onElementReady('.kwcDirectoriesListViewAjax', function(el, config) {
    config.renderTo = el.down('.viewContainer');
    el.select('.kwcDirectoriesListViewAjaxPaging').remove(); //remove paging, we will do endless scrolling instead
    el.kwcViewAjax = new Kwc.Directories.List.ViewAjax(config);
}, this, {
    priority: 0 //call *after* initializing kwcForm to have access to searchForm
});

//if there is no viewAjax that can handle the changed state reload current page
//this can happen if a reload has been between state navigations
Kwf.Utils.HistoryState.on('popstate', function() {
    if (Kwf.Utils.HistoryState.currentState.viewAjax) {
        var found = false;
        for (var componentId in Kwf.Utils.HistoryState.currentState.viewAjax) {
            if (Kwc.Directories.List.ViewAjax.byComponentId[componentId]) {
                found = true;
            }
        }
        if (!found) {
            location.href = location.href;
        }
    }
}, this);


Ext.ns('Kwc.Directories.List');
Kwc.Directories.List.ViewAjax = Ext.extend(Ext.Panel, {

    controllerUrl: null,

    directoryUrl: null, //needed to implement back-link in detail without page load

    border: false,
    layout: 'fit',
    cls: 'posts',
    initComponent: function() {
        Kwc.Directories.List.ViewAjax.byComponentId[this.componentId] = this;
        Kwc.Directories.List.ViewAjax.byDirectoryViewComponentId[this.directoryViewComponentId] = this;
        this.view = new Kwc.Directories.List.ViewAjax.View({
            controllerUrl: this.controllerUrl,
            directoryUrl: this.directoryUrl,
            directoryComponentId: this.directoryComponentId,
            baseParams: {
                componentId: this.componentId
            },
            placeholder: this.placeholder
        });
        this.items = [this.view];

        this.renderTo.select('.noEntriesFound').remove(); //view renders own

        var records = [];
        this.renderTo.select('.kwfViewAjaxItem').each(function(el) {
            var id = el.dom.className.match(/kwfViewAjaxItem ([^ ]+)/);
            if (id) {
                id = id[1];
                records.push(new this.view.store.recordType({
                    id: id,
                    content: el.dom.innerHTML
                }));
            }
            el.remove();
        }, this);
        this.view.store.add(records);

        if (this.searchFormComponentId) {
            this.searchForm = Kwc.Form.formsByComponentId[this.searchFormComponentId];
        }

        if (this.searchForm) {

            this.view.applyBaseParams(this.searchForm.getValues());

            this.searchForm.on('fieldChange', function(f) {
                if (f instanceof Kwf.FrontendForm.TextField && f.getValue().length < 3) return; //minimum length

                var values = this.searchForm.getValues();
                var diffFound = false;
                for(var i in values) {
                    if (values[i] != this.view.getBaseParams()[i]) {
                        diffFound = true;
                        break;
                    }
                }
                if (diffFound) {
                    this.view.applyBaseParams(values);
                    this.view.load();
                    this.addHistoryEntryDeleayedTask.delay(2000);
                }
            }, this, { buffer: 500 });

            this.searchForm.on('beforeSubmit', function(f) {
                var values = this.searchForm.getValues();
                this.view.applyBaseParams(values);
                this.view.load();
                this.pushSearchFormHistoryState();
                return false;
            }, this);

            this.addHistoryEntryDeleayedTask = new Ext.util.DelayedTask(function() {
                this.pushSearchFormHistoryState();
            }, this);
        }

        if (!Kwf.Utils.HistoryState.currentState.viewAjax) Kwf.Utils.HistoryState.currentState.viewAjax = {};
        Kwf.Utils.HistoryState.currentState.viewAjax[this.componentId] = {};

        if (this.searchForm) {
            this._getState().searchFormValues = this.searchForm.getValues();
        }

        if (!Kwc.Directories.List.ViewAjax.filterLinks[this.componentId]) {
            Kwc.Directories.List.ViewAjax.filterLinks[this.componentId] = [];
        }

        //set menuLinkId to link that is current, be be able to set current again
        Kwc.Directories.List.ViewAjax.filterLinks[this.componentId].forEach(function(i) {
            if (Ext.fly(i).hasClass('current')) {
                this._getState().menuLinkId = i.id;
            }
        }, this);

        if (this.filterComponentId) {
            this._getState().viewFilter = this.filterComponentId;
            this.view.applyBaseParams({
                filterComponentId: this.filterComponentId
            });
            delete this.filterComponentId;
        }

        Kwf.Utils.HistoryState.updateState();


        Kwf.Utils.HistoryState.on('popstate', function() {
            if (!this._getState()) return;
            if (this.searchForm) {
                this.searchForm.setValues(this._getState().searchFormValues);
            }
            if (this._getState().viewDetail) {
                this.showDetail(this._getState().viewDetail);
            } else if (this._getState().viewFilter) {
                this.loadView({
                    filterComponentId: this._getState().viewFilter
                });
            } else {
                this.loadView({});
            }
            if (!this.view.visibleDetail && this.view._lastViewScrollPosition) {
                Ext.select('html, body').scrollTo('b', this.view._lastViewScrollPosition.top);
            }
            if (this._getState().menuLinkId) {
                Kwc.Directories.List.ViewAjax.filterLinks[this.componentId].forEach(function(i) {
                    Ext.fly(i).removeClass('current');
                }, this);
                var el = Ext.fly(this._getState().menuLinkId);
                if (el) {
                    el.addClass('current');
                }
            }
        }, this);

        Kwc.Directories.List.ViewAjax.superclass.initComponent.call(this);
    },

    afterRender: function() {
        Kwc.Directories.List.ViewAjax.superclass.afterRender.call(this);

        this.initialLoadingEl = this.el.createChild({cls:'initialLoading'});
        this.initialLoadingEl.enableDisplayMode();
        this.linkToTop = this.el.createChild({cls:'linkToTop'});
        this.linkToTop.enableDisplayMode();
        this.linkToTop.on('click', function() {
            window.scrollTo(0, 0);
        });

        this.onMenuItemChanged();
    },

    pushSearchFormHistoryState: function()
    {
        this.addHistoryEntryDeleayedTask.cancel();
        this._getState().searchFormValues = this.searchForm.getValues();
        var url = location.protocol+'//'+location.host+this.viewUrl+'?'+Ext.urlEncode(this.searchForm.getValuesIncludingPost());
        Kwf.Utils.HistoryState.pushState(document.title, url);
    },

    onMenuItemChanged: function()
    {
        /*
        this.blockReload = true; //for filter changevalue so they won't reload too
        if (this.searchForm) {
            this.searchForm.clearValues();
        }
        this.blockReload = false;
        */
    },

    _getState: function()
    {
        return Kwf.Utils.HistoryState.currentState.viewAjax[this.componentId];
    },

    loadView: function(p)
    {
        var params = Ext.applyIf(p, {
            filterComponentId: null
        });
        if (this.searchForm) {
            Ext.apply(params, this.searchForm.getValues());
        }
        var diffFound = false;
        for(var i in params) {
            if (params[i] != this.view.getBaseParams()[i]) {
                diffFound = true;
                break;
            }
        }
        if (diffFound) {
            this.view.applyBaseParams(params);
            this.view.load();
        }
        this.onMenuItemChanged();
        this.view.showView();
    },

    showSearch: function(q)
    {
        this.loadView('search', { query: q });
    },

    showDetail: function(id) {
        this.view.showDetail(id);
    }

});

Kwc.Directories.List.ViewAjax.byComponentId = {};
Kwc.Directories.List.ViewAjax.byDirectoryViewComponentId = {};
Kwc.Directories.List.ViewAjax.filterLinks = {};

Kwc.Directories.List.ViewAjax.View = Ext.extend(Kwf.Binding.AbstractPanel,
{
    layout: 'fit',
    border: false,
    autoHeight: true,

    initComponent : function()
    {
        if (!this.tpl) {
            this.tpl = new Ext.XTemplate(
                '<tpl for=".">',
                    '<div class="kwfViewAjaxItem">{content}</div>',
                '</tpl>'
            );
        }

        this.store = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({ url: this.controllerUrl + '/json-data' }),
            reader: new Ext.data.JsonReader({
                totalProperty: 'total',
                root: 'rows',
                id: 'id',
                sucessProperty: 'success',
                fields: [{
                    name: 'content'
                },{
                    name: 'id'
                }]
            }),
            remoteSort: true,
            pruneModifiedRecords: true
        });
        if (this.baseParams) {
            this.setBaseParams(this.baseParams);
            delete this.baseParams;
        }

        this.store.newRecords = []; //hier werden neue records gespeichert die nicht dirty sind

        this.store.on('loadexception', function(proxy, o, response, e) {
            throw e; //re-throw
        }, this);
        
        this.store.on('load', function(s) {
            var valueElement = this.el.parent('.kwcDirectoriesListViewAjax').child('.kwcDirectoriesListViewCount .totalValue');
            if (valueElement) {
                valueElement.update(this.store.getTotalCount());
            }
        }, this);

        var viewConfig = {
            store: this.store,
            tpl: this.tpl,
            cls: 'kwfView',
            itemSelector: 'div.kwfViewAjaxItem',
            emptyText: '<span class="noEntriesFound">'+this.placeholder.noEntriesFound+'</span>',
            deferEmptyText: false,
            singleSelect: false,
            border: false,
            autoHeight: true
        };

        this.view = new Ext.DataView(viewConfig);
        this.view.updateIndexes = this.view.updateIndexes.createSequence(function() {
            Kwf.callOnContentReady(this.view.el);
        }, this);

        this.view.on('click', this.onItemClick, this);

        this.items = [ this.view ];

        Kwc.Directories.List.ViewAjax.View.superclass.initComponent.call(this);

        Ext.fly(window).on('scroll', function() {
            var height = this.el.getTop()+this.el.getHeight();
            height -= Ext.getBody().getViewSize().height;
            height = height - Ext.getBody().getScroll().top;
            if (height < 700) {
                this.loadMore();
            }
        }, this, { buffer: 50 });

        Ext.fly(window).on('scroll', function() {
            var scrollHeight = Ext.getBody().getScroll().top;
            if (scrollHeight >= 1700) {
                this.el.up('.viewContainer').addClass('scrolledDown');
            } else {
                this.el.up('.viewContainer').removeClass('scrolledDown');
            }
        }, this);
    },

    afterRender: function() {
        Kwc.Directories.List.ViewAjax.View.superclass.afterRender.call(this);

        this.kwfMainContent = this.el.parent('.kwfMainContent');
        if (this.kwfMainContent) {
            this.kwfMainContent.enableDisplayMode('block');
        }
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
        if (!params) params = {};
        if (!params.start) {
            params.start = 0;
        }
        if (this.el) this.el.mask('<img src="/assets/kwf/Kwf_js/EyeCandy/Lightbox/loading.gif" width="66" height="66" />', 'loading');
        this.getStore().load({
            params: params,
            callback: function() {
                if (this.el) this.el.unmask();
            },
            scope: this
        });
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
        if (!target) return;
        var m = target.dom.rel.match(/kwfDetail([^ ]+)/);
        if (!m) return;
        var config = Ext.decode(m[1]);
        if (!config.directoryComponentId) return;
        if (config.directoryComponentId != this.directoryComponentId) return;

        ev.stopEvent();
        //more... Link clicked
        this._lastViewScrollPosition = Ext.getBody().getScroll();
        this.showDetail(target.dom.href);
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

        if (this.ownerCt._getState().viewDetail != href) {
            this.ownerCt._getState().viewDetail = href;
            Kwf.Utils.HistoryState.pushState(document.title, href);
        }

        this.hideDetail();

        this.classNames = this.el.up('.kwcDirectoriesListViewAjax').dom.className;

        this.kwfMainContent.hide();
        this.detailEl = this.el.createChild({
            cls: 'kwfMainContent loadingContent ' +this.classNames,
            tag: 'div',
            style: 'width: ' + this.kwfMainContent.getStyle('width'),
            html: '<div class="loading"></div>'
        }, this.kwfMainContent);
        Ext.Ajax.request({
            url: Kwf.getKwcRenderUrl(),
            params: { url: href },
            success: function(response, options) {
                if (!this.detailEl) return;
                this.detailEl.removeClass('loadingContent '+this.classNames);
                this.detailEl.update(response.responseText);
                Kwf.Statistics.count(href);

                this.detailEl.query('a').forEach(function(el) {
                    if (el.href == location.protocol+'//'+location.host+this.directoryUrl) {
                        el.kwfViewAjaxInitDone = true;
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

                Kwf.callOnContentReady(this.detailEl, {newRender: true});
            },
            scope: this
        });
    },

    showView: function() {
        if (!this.visibleDetail) return;
        this.visibleDetail = null;

        this.hideDetail();

        this.kwfMainContent.show();

        this.ownerCt.onMenuItemChanged();
    }
});

