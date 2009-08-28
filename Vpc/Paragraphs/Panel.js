Ext.namespace('Vpc.Paragraphs');
Vpc.Paragraphs.Panel = Ext.extend(Vps.Binding.AbstractPanel,
{
    layout:'fit',
    cls: 'vpc-paragraphs',
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
            listeners: {
                scope: this,
                'delete': this.onDelete,
                edit: this.onEdit,
                changeVisible: this.onChangeVisible,
                changePos: this.onChangePos,
                addParagraphMenuShow: this.onAddParagraphMenuShow,
                addParagraph: this.onParagraphAdd
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
                this.dataView.refresh();
            },
            scope: this
        });
        this.actions.showVisible = new Ext.Action({
            text : trlVps('Show'),
            icon : '/assets/silkicons/monitor.png',
            cls  : 'x-btn-text-icon',
            menu : [{
                text: trlVps('all paragraphs'),
                group: 'showVisible',
                checked: true,
                handler: function() {
                    this.applyBaseParams({
                        filter_visible: null
                    });
                    this.load();
                },
                scope: this
            }, {
                text: trlVps('visible paragraphs'),
                group: 'showVisible',
                checked: false,
                handler: function() {
                    this.applyBaseParams({
                        filter_visible: 1
                    });
                    this.load();
                },
                scope: this
            }]
        });
        this.actions.addparagraph = new Vpc.Paragraphs.AddParagraphButton({
            components: this.components,
            componentIcons: this.componentIcons,
            listeners: {
                scope: this,
                menushow: function() {
                    if (this.store.getCount() == 0) {
                        this.addParagraphPos = 1;
                    } else {
                        this.addParagraphPos = this.store.getAt(this.store.getCount()-1).get('pos')+1;
                    }
                },
                addParagraph: function(component) {
                    this.onParagraphAdd(component);
                }
            }
        });

        this.tbar = [ this.actions.showPreview, '-', this.actions.showVisible, '-', this.actions.addparagraph ];


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
        this.fireEvent('gotComponentConfigs', result.componentConfigs);
        Ext.applyIf(this.dataView.componentConfigs, result.componentConfigs);

        var meta = result.metaData;
        this.metaData = meta;

        var storeConfig = {
            proxy: new Ext.data.HttpProxy({ url: this.controllerUrl + '/json-data' }),
            reader: new Ext.data.JsonReader({
                totalProperty: meta.totalProperty,
                root: meta.root,
                id: meta.id,
                sucessProperty: meta.successProperty,
                fields: meta.fields
            }),
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
    }

});

Ext.reg('vpc.paragraphs', Vpc.Paragraphs.Panel);
