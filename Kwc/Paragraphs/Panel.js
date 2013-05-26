Ext.namespace('Kwc.Paragraphs');


Kwc.Paragraphs.PanelJsonReader = Ext.extend(Ext.data.JsonReader,
{
    readRecords: function(o) {
        var ret = Kwc.Paragraphs.PanelJsonReader.superclass.readRecords.apply(this, arguments);
        if (o.componentConfigs) {
            this.paragraphsPanel.fireEvent('gotComponentConfigs', o.componentConfigs);
            Ext.applyIf(this.paragraphsPanel.dataView.componentConfigs, o.componentConfigs);
        }
        if (o.contentWidth) {
            this.paragraphsPanel.dataView.setWidth(o.contentWidth + 20);
        }
        return ret;
    }
});

Kwc.Paragraphs.Panel = Ext.extend(Kwf.Binding.AbstractPanel,
{
    layout:'auto',
    cls: 'kwc-paragraphs',
    showDelete: true,
    showPosition: true,
    showCopyPaste: true,
    _loadingCount: 0,
    initComponent : function()
    {
        this.addEvents('editcomponent', 'gotComponentConfigs');

        if (this.autoLoad !== false) {
            this.autoLoad = true;
        } else {
            delete this.autoLoad;
        }

        this.dataView = new Kwc.Paragraphs.DataView({
            components: this.components,
            componentIcons: this.componentIcons,
            showDelete: this.showDelete,
            showDeviceVisible: this.showDeviceVisible,
            showPosition: this.showPosition,
            showCopyPaste: this.showCopyPaste,
            listeners: {
                scope: this,
                'delete': this.onDelete,
                edit: this.onEdit,
                changeVisible: this.onChangeVisible,
                changeDeviceVisible: this.onChangeDeviceVisible,
                changePos: this.onChangePos,
                addParagraphMenuShow: this.onAddParagraphMenuShow,
                addParagraph: this.onParagraphAdd,
                copyParagraph: this.onCopyParagraph,
                pasteParagraph: this.onPasteParagraph,
                copyPasteMenuShow: this.onCopyPasteMenuShow
            }
        });

        this.items = [ this.dataView ];

        this.actions.showPreview = new Ext.Action({
            text : trlKwf('Preview'),
            icon : '/assets/silkicons/zoom.png',
            cls  : 'x-btn-text-icon',
            enableToggle: true,
            handler: function(b) {
                this.dataView.showToolbars = !b.pressed;
                if (b.pressed) {
                    this.store.filterBy(function(r) { return !!r.get('visible'); }, this);
                } else {
                    this.store.filterBy(function(r) { return true }, this);
                }
            },
            scope: this
        });

        this.actions.showPreviewWeb = new Ext.Action({
            text : trlKwf('Preview in web'),
            icon : '/assets/silkicons/zoom_in.png',
            cls  : 'x-btn-text-icon',
            handler: function(b) {
                window.open(this.controllerUrl+'/open-preview?componentId='+
                    this.getBaseParams().componentId);
            },
            scope: this
        });

        this.actions.makeAllVisible = new Ext.Action({
            text : trlKwf('All Visible'),
            icon : '/assets/silkicons/tick.png',
            cls  : 'x-btn-text-icon',
            handler: function(b) {
                Ext.Msg.show({
                    title: trlKwf('All Visible'),
                    msg: trlKwf('Do you really wish to set everything to visible?'),
                    buttons: Ext.Msg.YESNO,
                    scope: this,
                    fn: function(button) {
                        if (button == 'yes') {
                            Ext.Ajax.request({
                                mask: this.el,
                                maskText: trlKwf('Setting visible...'),
                                url: this.controllerUrl+'/json-make-all-visible',
                                params: this.getBaseParams(),
                                success: function() {
                                    this.reload();
                                },
                                scope: this
                            });
                        }
                    }
                });
            },
            scope: this
        });

        if (this.components) {
            this.actions.addparagraph = new Kwc.Paragraphs.AddParagraphButton({
                components: this.components,
                componentIcons: this.componentIcons,
                listeners: {
                    scope: this,
                    menushow: function() {
                        this.addParagraphPos = 1;
                    },
                    addParagraph: function(component) {
                        this.onParagraphAdd(component);
                    }
                }
            });
            this.actions.copyPaste = {
                text: trlKwf('copy/paste'),
                menu: [{
                    text: trlKwf('Copy all Paragraphs'),
                    icon: '/assets/silkicons/page_white_copy.png',
                    scope: this,
                    handler: function() {
                        this.onCopyAllParagraphs();
                    }
                },{
                    text: trlKwf('Paste Paragraph'),
                    icon: '/assets/silkicons/page_white_copy.png',
                    scope: this,
                    handler: function() {
                        this.onPasteParagraph();
                    }
                }],
                icon: '/assets/silkicons/page_white_copy.png',
                cls  : 'x-btn-text-icon',
                listeners: {
                    scope: this,
                    menushow: function(btn) {
                        this.copyPasteParagraphPos = 1;
                    }
                }
            };
        }

        this.tbar = [ this.actions.showPreview, this.actions.showPreviewWeb ];
        if (this.actions.addparagraph) {
            this.tbar.push('-');
            this.tbar.push(this.actions.addparagraph);
            if (this.showCopyPaste) {
                this.tbar.push(this.actions.copyPaste);
            }
        }
        this.tbar.push('->');
        this._loading = new Ext.Button({
            iconCls: "x-tbar-loading",
            disabled: true,
            text: trlKwf('Saving')
        });
        this.tbar.push(this._loading);
        this._loading.hide();

        this.tbar.push(this.actions.makeAllVisible);

        Kwc.Paragraphs.Panel.superclass.initComponent.call(this);
    },

    doAutoLoad : function()
    {
        //autoLoad kann in der zwischenzeit abgeschaltet werden, zB wenn
        //wir in einem Binding sind
        if (!this.autoLoad) return;
        this.load();
    },

    load: function(params, options) {
        if (!params) params = {};
        if (!this.store) {
            Ext.applyIf(params, this.baseParams);
            params.meta = true;
            Ext.Ajax.request({
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
            this.store.load({
                params: params
            });
        }
    },

    onMetaLoad : function(result)
    {
        //this.fireEvent('gotComponentConfigs', result.componentConfigs);
        //Ext.applyIf(this.dataView.componentConfigs, result.componentConfigs);

        var meta = result.metaData;
        this.metaData = meta;

        var reader = new Kwc.Paragraphs.PanelJsonReader({
            totalProperty: meta.totalProperty,
            root: meta.root,
            id: meta.id,
            sucessProperty: meta.successProperty,
            fields: meta.fields
        });
        reader.paragraphsPanel = this;
        var storeConfig = {
            proxy: new Ext.data.HttpProxy({ url: this.controllerUrl + '/json-data' }),
            reader: reader,
            sortInfo: meta.sortInfo,
            remoteSort: false
        };
        this.store = new Ext.data.Store(storeConfig);
        if (this.baseParams) {
            this.setBaseParams(this.baseParams);
            delete this.baseParams;
        }
        this.dataView.setStore(this.store);
        this.loadMask = new Ext.LoadMask(this.bwrap,
                Ext.apply({store:this.store}, this.loadMask));

        if (result.rows) {
            this.store.loadData(result);
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

    //protected, zum überschreiben in unterklassen um zusäztliche daten zu speichern
    getSaveParams : function()
    {
        var data = [];
        var modified = this.store.getModifiedRecords();
        if (!modified.length) return {};
        modified.each(function(r) {
            data.push(r.data);
        }, this);
        var params = this.getBaseParams() || {};
        params.data = Ext.util.JSON.encode(data);
        return params;
    },

    _showLoading: function()
    {
        this._loadingCount++;
        this._loading.show();
    },
    _hideLoading: function() {
        this._loadingCount--;
        if (this._loadingCount <= 0) {
            this._loading.hide();
        }
    },

    submit : function()
    {
        var params = this.getSaveParams();

        //gibts da keine bessere l�sung?
        var empty = true;
        for (var i in params) {
            empty = false;
            break;
        }
        if (empty) return;

        this._loading.show();

        if (!this._submitTask) {
            this._submitTask = new Ext.util.DelayedTask(function(){
                this._doSubmit();
            }, this);
        }
        this._submitTask.delay(500);
    },

    _doSubmit: function() {

        this._showLoading();

        var params = this.getSaveParams();
        this.store.modified = [];


        Ext.Ajax.request({
            url: this.controllerUrl+'/json-save',
            params: params,
            success: function(response, options, r) {
                //geänderte und neue zurücksetzen, damit isDirty false ist
                this.fireEvent('datachange', r);
            },
            callback: function() {
                this._hideLoading();
            },
            scope  : this
        });
    },

    onChangeVisible: function(record) {
        record.set('visible', !record.get('visible'));
        this.submit();
    },

    onChangeDeviceVisible: function(record, value) {
        record.set('device_visible', value);
        this.submit();
    },

    onChangePos: function(record, pos) {
        record.set('pos', pos);

        var p = 1;
        for (var i=0;i<this.store.getCount();i++, p++) {
            var r = this.store.getAt(i);
            if (p == pos) {
                p++;
                //set above
            }
            if (r == record) {
                //already set
                if (pos > p) p--;
                continue;
            } else {
                if (r.get('pos') != p) {
                    r.set('pos', p);
                }
            }
        }
        this.store.sort('pos', 'ASC');
        this.submit();
    },

    onDelete : function(record) {
        Ext.Msg.show({
            title: trlKwf('Delete'),
            msg: trlKwf('Do you really wish to remove this paragraph?'),
            buttons: Ext.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    this.store.remove(record);
                    var pos = 1;
                    this.store.each(function(r) {
                        if (r.get('pos') != pos) r.set('pos', pos);
                        pos++;
                    }, this);
                    var params = this.getBaseParams();
                    params.id = record.get('id');
                    this._showLoading();
                    Ext.Ajax.request({
                        url: this.controllerUrl+'/json-delete',
                        params: params,
                        success: function(response, options, r) {
                        },
                        callback: function() {
                            this._hideLoading();
                        },
                        scope : this
                    });
                }
            }
        });
    },

    onEdit : function(record, editComponent) {
        var bp = this.getBaseParams();
        editComponent.componentId = bp.componentId + '-' + record.get('id');
        editComponent.editComponents = record.get('edit_components');
        this.fireEvent('editcomponent', editComponent);
    },

    onAddParagraphMenuShow: function(record) {
        this.addParagraphPos = parseInt(record.get('pos'))+1;
    },

    onParagraphAdd : function(component)
    {
        var params = this.getBaseParams();
        params.pos = this.addParagraphPos;
        params.component = component;
        Ext.Ajax.request({
            url: this.controllerUrl + '/json-add-paragraph',
            params: params,
            success: function(response, options, result) {
                this.fireEvent('gotComponentConfigs', result.componentConfigs);
                var opened = false;
                result.editComponents.forEach(function(ec) {
                    if (result.openConfigKey == ec.type) {
                        var data = Kwf.clone(ec);
                        data.componentId = this.getBaseParams().componentId + '-' + result.id;
                        data.editComponents = result.editComponents;
                        this.fireEvent('editcomponent', data);
                        opened = true;
                        return false;
                    }
                }, this);
                if (!opened) {
                    this.reload();
                }
            },
            scope: this
        });
    },

    onCopyParagraph: function(record) {
        var params = Kwf.clone(this.getBaseParams());
        params.id = record.get('id');
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-copy',
            params: params,
            mask: this.el
        });
    },

    onPasteParagraph: function() {
        var params = Kwf.clone(this.getBaseParams());
        params.pos = this.copyPasteParagraphPos;
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-paste',
            params: params,
            mask: this.el,
            scope: this,
            progress: true,
            progressTitle : trlKwf('Paste Paragraph'),
            success: function() {
                this.reload();
                this.fireEvent('datachange');
            }
        });
    },

    onCopyPasteMenuShow: function(record) {
        this.copyPasteParagraphPos = parseInt(record.get('pos'))+1;
    },

    onCopyAllParagraphs: function() {
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-copy-all',
            params: this.getBaseParams(),
            mask: this.el
        });
    }

});

Ext.reg('kwc.paragraphs', Kwc.Paragraphs.Panel);
