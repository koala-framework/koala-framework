Ext.namespace('Vpc.Paragraphs');


Vpc.Paragraphs.PanelJsonReader = Ext.extend(Ext.data.JsonReader,
{
    readRecords: function(o) {
        var ret = Vpc.Paragraphs.PanelJsonReader.superclass.readRecords.apply(this, arguments);
        if (o.componentConfigs) {
            this.paragraphsPanel.fireEvent('gotComponentConfigs', o.componentConfigs);
            Ext.applyIf(this.paragraphsPanel.dataView.componentConfigs, o.componentConfigs);
        }
        return ret;
    }
});

Vpc.Paragraphs.Panel = Ext.extend(Vps.Binding.AbstractPanel,
{
    layout:'fit',
    cls: 'vpc-paragraphs',
    showDelete: true,
    showPosition: true,
    initComponent : function()
    {
        this.addEvents('editcomponent', 'gotComponentConfigs');

        if (this.autoLoad !== false) {
            this.autoLoad = true;
        } else {
            delete this.autoLoad;
        }

        this.dataView = new Vpc.Paragraphs.DataView({
            components: this.components,
            componentIcons: this.componentIcons,
            width: this.previewWidth,
            showDelete: this.showDelete,
            showPosition: this.showPosition,
            showCopyPaste: this.showCopyPaste,
            listeners: {
                scope: this,
                'delete': this.onDelete,
                edit: this.onEdit,
                changeVisible: this.onChangeVisible,
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
            text : trlVps('Preview'),
            icon : '/assets/silkicons/zoom.png',
            cls  : 'x-btn-text-icon',
            enableToggle: true,
            handler: function(b) {
                this.dataView.showToolbars = !b.pressed;
                if (b.pressed) {
                    this.applyBaseParams({
                        filter_visible: 1
                    });
                } else {
                    this.applyBaseParams({
                        filter_visible: null
                    });
                }
                this.load();
            },
            scope: this
        });

        this.actions.makeAllVisible = new Ext.Action({
            text : trlVps('All Visible'),
            icon : '/assets/silkicons/tick.png',
            cls  : 'x-btn-text-icon',
            handler: function(b) {
                Ext.Msg.show({
                    title: trlVps('All Visible'),
                    msg: trlVps('Do you really wish to set everything to visible?'),
                    buttons: Ext.Msg.YESNO,
                    scope: this,
                    fn: function(button) {
                        if (button == 'yes') {
                            Ext.Ajax.request({
                                mask: this.el,
                                maskText: trlVps('Setting visible...'),
                                url: this.controllerUrl+'/json-make-all-visible',
                                params: this.getBaseParams(),
                                success: function() {
                                    this.reload();
                                },
                                scope: this
                            });
                        }
                    }
                })
            },
            scope: this
        });

        if (this.components) {
            this.actions.addparagraph = new Vpc.Paragraphs.AddParagraphButton({
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
                text: trlVps('copy/paste'),
                menu: [{
                    text: trlVps('Copy Paragraph'),
                    icon: '/assets/silkicons/page_white_copy.png',
                    disabled: true
                },{
                    text: trlVps('Paste Paragraph'),
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

        this.tbar = [ this.actions.showPreview ];
        if (this.actions.addparagraph) {
            this.tbar.push('-');
            this.tbar.push(this.actions.addparagraph);
            if (this.showCopyPaste) {
                this.tbar.push(this.actions.copyPaste);
            }
        }
        this.tbar.push('->');
        this.tbar.push(this.actions.makeAllVisible);

        Vpc.Paragraphs.Panel.superclass.initComponent.call(this);
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

        var reader = new Vpc.Paragraphs.PanelJsonReader({
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
            remoteSort: true
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

        this.el.mask(trlVps('Saving...'));

        Ext.Ajax.request({
            url: this.controllerUrl+'/json-save',
            params: params,
            success: function(response, options, r) {
                //geänderte und neue zurücksetzen, damit isDirty false ist
                this.store.modified = [];
                this.reload();
                this.fireEvent('datachange', r);
            },
            callback: function() {
                this.el.unmask();
            },
            scope  : this
        });
    },

    onChangeVisible: function(record) {
        record.set('visible', !record.get('visible'));
        this.submit();
    },

    onChangePos: function(record, pos) {
        record.set('pos', pos);
        this.submit();
    },

    onDelete : function(record) {
        Ext.Msg.show({
            title: trlVps('Delete'),
            msg: trlVps('Do you really wish to remove this paragraph?'),
            buttons: Ext.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    this.el.mask(trlVps('Deleting...'));
                    var params = this.getBaseParams();
                    params.id = record.get('id');
                    Ext.Ajax.request({
                        url: this.controllerUrl+'/json-delete',
                        params: params,
                        success: function(response, options, r) {
                            this.reload();
                        },
                        callback: function() {
                            this.el.unmask();
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
                if (result.editComponents.length) {
                    var data = Vps.clone(result.editComponents[0]);
                    data.componentId = this.getBaseParams().componentId + '-' + result.id;
                    data.editComponents = result.editComponents;
                    this.fireEvent('editcomponent', data);
                } else {
                    this.reload();
                }
            },
            scope: this
        });
    },

    onCopyParagraph: function(record) {
        var params = Vps.clone(this.getBaseParams());
        params.id = record.get('id');
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-copy',
            params: params,
            mask: this.el
        });
    },

    onPasteParagraph: function() {
        var params = Vps.clone(this.getBaseParams());
        params.pos = this.copyPasteParagraphPos;
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-paste',
            params: params,
            mask: this.el,
            scope: this,
            success: function() {
                this.reload();
                this.fireEvent('datachange');
            }
        });
    },

    onCopyPasteMenuShow: function(record) {
        this.copyPasteParagraphPos = parseInt(record.get('pos'))+1;
    }

});

Ext.reg('vpc.paragraphs', Vpc.Paragraphs.Panel);
