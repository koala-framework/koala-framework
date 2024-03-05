Kwf.Auto.ImageGridPanel = Ext2.extend(Kwf.Binding.AbstractPanel,
{
    layout: 'fit',

    initComponent : function()
    {
        if (!this.viewConfig) this.viewConfig = { plugins: [] };
        if (this.autoLoad !== false) {
            this.autoLoad = true;
        } else {
            delete this.autoLoad;
        }

        if (!this.tpl) {
            this.tpl = new Ext2.XTemplate(
                '<tpl for=".">',
                    '<div class="thumb-wrap">',
                        '<table class="thumb" cellpadding="0" cellspacing="3" border="0"><tr><td>',
                            '<tpl if="src"><img src="{src:htmlEncode}" alt="{label:htmlEncode}" /></tpl>',
                        '</td></tr></table>',
                        '<div class="label">{label_short:htmlEncode}</div>',
                    '</div>',
                '</tpl>',
                '<div class="x2-clear"></div>'
            );
        }

        if (!this.bigPreviewTpl) {
            this.bigPreviewTpl = new Ext2.XTemplate(
                '<div class="big-wrap">',
                    '<div class="label">{label:htmlEncode}</div>',
                    '<div class="imageBig"><img src="{src_large:htmlEncode}" alt="{label:htmlEncode}" width="{src_large_width:htmlEncode}" height="{src_large_height:htmlEncode}" /></div>',
                '</div>'
            );
        }

        if (!this.prepareViewData) {
            this.prepareViewData = function(data) {
                return data;
            };
        }

        this.actions.reload = new Ext2.Action({
            icon    : '/assets/silkicons/arrow_rotate_clockwise.png',
            cls     : 'x2-btn-icon',
            tooltip : trlKwf('Reload'),
            handler : this.reload,
            scope   : this
        });
        this.actions.add = new Ext2.Action({
            text    : trlKwf('Add'),
            icon    : '/assets/silkicons/table_add.png',
            cls     : 'x2-btn-text-icon',
            handler : this.onAdd,
            scope: this
        });
        this.actions['delete'] = new Ext2.Action({
            text    : trlKwf('Delete'),
            icon    : '/assets/silkicons/table_delete.png',
            cls     : 'x2-btn-text-icon',
            handler : this.onDelete,
            scope: this,
            needsSelection: true
        });
        Kwf.Auto.ImageGridPanel.superclass.initComponent.call(this);
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
            var remoteSort = false;
            if (meta.paging) remoteSort = true;
            if (!meta.sortable) remoteSort = true;
            var storeConfig = {
                proxy: new Ext2.data.HttpProxy({ url: this.controllerUrl + '/json-data' }),
                reader: new Ext2.data.JsonReader({
                    totalProperty: meta.totalProperty,
                    root: meta.root,
                    id: meta.id,
                    sucessProperty: meta.successProperty,
                    fields: meta.fields
                }),
                remoteSort: remoteSort,
                sortInfo: meta.sortInfo,
                pruneModifiedRecords: true
            };
            this.store = new Ext2.data.Store(storeConfig);
            if (this.baseParams) {
                this.setBaseParams(this.baseParams);
                delete this.baseParams;
            }
        }

        this.store.on('beforeload', function() {
            this._selectedIdForReselect = null;
            this._selectedIdForReselect = this.getSelectedId();
        }, this);

        this.store.on('load', function() {
            if (this._selectedIdForReselect) {
                this.selectId(this._selectedIdForReselect);
            }
        }, this);

        this.store.newRecords = []; //hier werden neue records gespeichert die nicht dirty sind
        this.store.on('update', function(store, record, operation) {
            if (operation == Ext2.data.Record.EDIT) {
                if (this.isDirty()) {
                    this.getAction('save').enable();
                } else {
                    this.getAction('save').disable();
                }
            }
        }, this);
        this.relayEvents(this.store, ['load']);
        this.on('aftereditcomplete', function() {
            if (this.isDirty()) {
                this.getAction('save').enable();
            } else {
                this.getAction('save').disable();
            }
        }, this);
        this.on('beforeedit', function() {
            this.getAction('save').enable();
        }, this);
        this.store.on('add', function(store, records, index) {
            this.getAction('save').enable();
        }, this);

        this.store.on('loadexception', function(proxy, o, response, e) {
            throw e; //re-throw
        }, this);

        this.relayEvents(this.store, ['load']);

        var viewConfig = Ext2.applyIf(this.viewConfig, {
            store: this.store,
            tpl: this.tpl,
            cls: 'imageGrid',
            autoHeight: true,
            itemSelector: 'div.thumb-wrap',
            emptyText: trlKwf('No items to display'),
            plugins: [],
            prepareData: this.prepareViewData,
            singleSelect: true,
            border: false,
            listeners: { scope: this }
        });

        if (meta.paging) {
            if (typeof meta.paging == 'object') {
                var t;
                if (meta.paging.type && Kwf.PagingToolbar[meta.paging.type]) {
                    this.pagingType = meta.paging.type;
                    t = Kwf.PagingToolbar[meta.paging.type];
                } else if(meta.paging.type) {
                    try {
                        t = eval(meta.paging.type);
                    } catch(e) {
                        throw "invalid paging-toolbar: "+meta.paging.type;
                    }
                    this.pagingType = meta.paging.type;
                } else {
                    this.pagingType = 'Ext2.PagingToolbar';
                    t = Ext2.PagingToolbar;
                }
                delete meta.paging.type;
                var pagingConfig = meta.paging;
                pagingConfig.store = this.store;
                this.bottomToolBar = new t(pagingConfig);
            } else {
                this.pagingType = 'Ext2.PagingToolbar';
                this.bottomToolBar = new Ext2.PagingToolbar({
                    store: this.store,
                    pageSize: meta.paging,
                    displayInfo: true
                });
            }
        } else {
            this.pagingType = false;
        }

        for (var i in this.actions) {
            if (this.actions[i].initialConfig.needsSelection) {
                this.actions[i].disable();
            }
            if (!meta.permissions[i]) {
                this.getAction(i).hide();
            }
        }

        //aktionen die als string in der tbar sind
        if (!this.topToolBar) this.topToolBar = [ ];
        var existingActions = {};
        for (var i = 0; i < this.topToolBar.length; i++) {
            if (typeof this.topToolBar[i] == 'string'
                    && this.getAction(this.topToolBar[i])) {
                existingActions[this.topToolBar[i]] = true;
            }
        }

        if (meta.buttons.add) {
            if (!existingActions.add) {
                this.topToolBar.add(this.getAction('add'));
            }
            delete meta.buttons.add;
        }
        if (meta.buttons['delete']) {
            if (!existingActions['delete']) {
                this.topToolBar.add(this.getAction('delete'));
            }
            delete meta.buttons['delete'];
        }

        for (var i in meta.buttons) {
            if (i != 'reload' && !existingActions[i]) {
                if (this.getAction(i)) {
                    this.topToolBar.add(this.getAction(i));
                } else {
                    this.topToolBar.add(i);
                }
            }
        }

        for (var i = 0; i < this.topToolBar.length; i++) {
            if (typeof this.topToolBar[i] == 'string'
                    && this.getAction(this.topToolBar[i])) {
                this.topToolBar[i] = this.getAction(this.topToolBar[i]);
            }
        }

        this.filters = new Kwf.Auto.FilterCollection(meta.filters, this);
        this.filters.each(function(filter) {
            filter.on('filter', function(f, params) {
                this.applyBaseParams(params);
                this.load();
            }, this);
        }, this);
        this.filters.applyToTbar(this.topToolBar);

        if (meta.buttons.reload) {
            this.topToolBar.add('->');
        }
        if (meta.buttons.reload && !existingActions.reload) {
            this.topToolBar.add(this.getAction('reload'));
        }

        if (meta.helpText) {
            this.topToolBar.add('->');
            this.topToolBar.add(new Ext2.Action({
                icon : '/assets/silkicons/information.png',
                cls : 'x2-btn-icon',
                handler : function (a) {
                    var helpWindow = new Ext2.Window({
                        html: meta.helpText,
                        width: 400,
                        bodyStyle: 'padding: 10px; background-color: white;',
                        autoHeight: true,
                        bodyBorder : false,
                        title: trlKwf('Info'),
                        resize: false
                    });
                    helpWindow.show();
                },
                scope: this
            }));
        }
        //wenn toolbar leer und keine tbar über config gesetzt dann nicht erstellen
        if (this.topToolBar.length == 0) {
            delete this.topToolBar;
        }

        this.view = new Ext2.DataView(viewConfig);
        this.relayEvents(this.view, ['selectionchange', 'beforeselect']);

        this.store.on('beforeload', function() {
            this.el.mask(trlKwf('Loading...'));
        }, this);
        this.store.on('load', function(store, records, opts) {
            this.el.unmask();

            Ext2.apply(Ext2.QuickTips.getQuickTip(), {
                maxWidth: 420,
                minWidth: 100
            });

            var compiledTpl = this.bigPreviewTpl.compile();
            for (var i = 0; i < records.length; i++) {
                Ext2.QuickTips.register({
                    target: Ext2.get(this.view.getNode(i)).child('img'),
                    text: compiledTpl.apply(records[i].data),
                    dismissDelay: 40000
                });
            }
        }, this);

        this.view.on('selectionchange', function() {
            if (this.getSelectedId()) {
                for (var i in this.actions) {
                    var a = this.actions[i];
                    if (a.initialConfig.needsSelection) a.enable();
                }
            } else {
                for (var i in this.actions) {
                    var a = this.actions[i];
                    if (a.initialConfig.needsSelection) a.disable();
                }
            }
        }, this);

        var panel = new Ext2.Panel({
            tbar: this.topToolBar ? this.topToolBar : null,
            bbar: this.bottomToolBar ? this.bottomToolBar : null,
            items: [ this.view],
            border: false,
            autoScroll: true
        });
        this.add(panel);
        this.doLayout();

        this.fireEvent('loaded', this.view);

        if (result.rows) {
            this.store.loadData(result);
        }
    },

    getFilter : function(filterName) {
        if (this.filters) return this.filters.get(filterName);
    },

    getStore : function() {
        return this.store;
    },

    //für AbstractPanel
    getSelectedId: function() {
        var s = this.view.getSelectedRecords();
        if (s && s[0]) return s[0].id;
        return null;
    },

    //für AbstractPanel
    selectId: function(id) {
        if (id) {
            var idx = this.getStore().indexOfId(id);
            if (idx !== -1) {
                this.view.select(idx);
            }
        } else {
            this.clearSelections();
        }
    },
    clearSelections: function() {
        if (this.view) {
            this.view.clearSelections();
        }
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
            Ext2.applyIf(params, Ext2.apply({ meta: true }, this.baseParams));
            if (!this.metaConn) this.metaConn = new Kwf.Connection({ autoAbort: true });
            this.metaConn.request({
                mask: true,
                url: this.controllerUrl+'/json-data',
                params: params,
                success: function(response, options, r) {
                    this.onMetaLoad(r);
                },
                scope: this
            });
        } else {
            if (this.pagingType && this.pagingType != 'Date' && !params.start) {
                params.start = 0;
            }
            this.getStore().load({ params: params });
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
            Ext2.apply(this.getStore().baseParams, baseParams);
        } else {
            //no store yet, apply them later
            if (!this.baseParams) this.baseParams = {};
            Ext2.apply(this.baseParams, baseParams);
        }
    },
    resetFilters: function() {
        this.filters.each(function(f) {
            f.reset();
            this.applyBaseParams(f.getParams());
        }, this);
    },
    isDirty: function() {
        if (!this.store) return false;
        if (this.store.getModifiedRecords().length || this.store.newRecords.legth) {
            return true;
        } else {
            return false;
        }
    },

    onAdd : function()
    {
        //im ersten form-binding hinzufügen
        this.bindings.each(function(b) {
            if (b.item instanceof Kwf.Auto.FormPanel) {
                b.item.onAdd();
                return false;
            }
        }, this);
        this.fireEvent('addaction', this);
    },

    onDelete : function() {
        Ext2.Msg.show({
            title: trlKwf('Delete'),
            msg: trlKwf('Do you really wish to remove this entry / these entries?'),
            buttons: Ext2.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    var selectedId = this.getSelectedId();
                    if (!selectedId) return;

                    var ids = [ selectedId ];
                    var params = this.getBaseParams() || {};
                    if (!ids.length) return;

                    params[this.store.reader.meta.id] = ids.join(';');

                    this.el.mask(trlKwf('Deleting...'));
                    Ext2.Ajax.request({
                        url: this.controllerUrl+'/json-delete',
                        params: params,
                        success: function(response, options, r) {
                            this.reload();
                            this.fireEvent('deleterow', this.view);
                            this.fireEvent('datachange', r);

                            //wenn gelöscht alle anderen disablen
                            this.bindings.each(function(i) {
                                i.item.disable();
                                i.item.reset();
                            }, this);
                        },
                        callback: function() {
                            this.el.unmask();
                        },
                        scope : this
                    });
                }
            }
        });
    }
});

Ext2.reg('kwf.imagegrid', Kwf.Auto.ImageGridPanel);
