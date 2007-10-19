Vps.Auto.GridPanel = Ext.extend(Ext.Panel,
{
    controllerUrl: '',
    //autoload: true,
    layout: 'fit',
    actions: {},
    filters: [],

    initComponent : function()
    {
        if (!this.gridConfig) this.gridConfig = { plugins: [] };

//         if(this.autoload) {
        //todo: wos bosiat bei !autoload

            if (!this.controllerUrl) {
                throw 'No controllerUrl specified for AutoGrid.';
            }
            Ext.Ajax.request({
                mask: true,
                url: this.controllerUrl+'jsonData',
                params: {meta: true},
                success: function(response, options, r) {
                    var result = Ext.decode(response.responseText);
                    this.onMetaLoad(result);
                },
                scope: this
            });
//         }

        this.addEvents({
            'rendergrid': true,
            'beforerendergrid': true,
            'deleterow': true
        });

        Vps.Auto.GridPanel.superclass.initComponent.call(this);
    },

    onMetaLoad : function(result)
    {
        var meta = result.metaData;
        this.metaData = meta;

        if (!this.store) {
            var remoteSort = false;
            if (meta.paging) remoteSort = true;
            var storeConfig = {
                proxy: new Ext.data.HttpProxy({url: this.controllerUrl + 'jsonData'}),
                reader: new Ext.data.JsonReader({
                    totalProperty: meta.totalProperty,
                    root: meta.root,
                    id: meta.id,
                    sucessProperty: meta.successProperty
                }, meta.fields),
                remoteSort: remoteSort,
                sortInfo: meta.sortInfo
            };
            if (meta.grouping) {
                var storeType = Ext.data.GroupingStore;
                storeConfig.groupField = meta.grouping.groupField;
                delete meta.grouping.groupField;
            } else {
                var storeType = Ext.data.Store;
            }
            this.store = new storeType(storeConfig);
        }

        this.store.newRecords = []; //hier werden neue records gespeichert die nicht dirty sind
        this.store.on('update', function(store, record, operation) {
            if (operation == Ext.data.Record.EDIT) {
                this.getAction('save').enable();
            }
        }, this);
        this.store.on('add', function(store, records, index) {
            this.getAction('save').enable();
        }, this);

        this.store.on('loadexception', function(proxy, o, response, e) {
            throw e; //re-throw
        }, this);

        if (!this.selModel) {
            this.selModel = new Ext.grid.RowSelectionModel({singleSelect:true});
        }

        this.selModel.on('rowselect', function(selData, gridRow, currentRow) {
            this.getAction('delete').enable();
        }, this);

        this.relayEvents(this.store, ['load']);
        this.relayEvents(this.selModel, ['rowselect', 'beforerowselect']);

        var gridConfig = Ext.applyIf(this.gridConfig, {
            store: this.store,
            selModel: this.selModel,
            clicksToEdit: 1,
            border: false,
            loadMask: true,
            plugins: [],
            tbar: [],
            listeners: { scope: this }
        });

        if (meta.grouping) {
            gridConfig.view = new Ext.grid.GroupingView(Ext.applyIf(meta.grouping, {
                forceFit: true
            }));

            if (!meta.grouping.noGroupSummary) {
                var found = false;
                gridConfig.plugins.each(function(p) {
                    if (p instanceof Ext.grid.GroupSummary) {
                        found = true;
                        return false;
                    }
                });
                if (!found) {
                    gridConfig.plugins.push(new Ext.grid.GroupSummary());
                }
            }
        } else {
            gridConfig.view = new Ext.grid.GridView({
                forceFit: true
            });
        }

        this.comboBoxes = [];

        var config = [];
        for (var i=0; i<meta.columns.length; i++) {
            var column = meta.columns[i];
            if (!column.header) continue;

            if (column.editor && column.editor.xtype == 'checkbox') {
                delete column.editor;
                if (column.renderer) delete column.renderer;
                column = new Ext.grid.CheckColumn(column);
                gridConfig.plugins.push(column);
            } else if (column.editor) {
                Ext.applyIf(column.editor, { msgTarget: 'qtip' });

                column.editor = new Ext.grid.GridEditor(Ext.ComponentMgr.create(column.editor, 'textfield'));
                var field = column.editor.field;
                if(field instanceof Ext.form.ComboBox) {
                    this.comboBoxes.push({
                        field: field,
                        column: column
                    });
                }
            }

            if (typeof column.renderer == 'function') {
                //do nothing
            } else if (Ext.util.Format[column.renderer]) {
                column.renderer = Ext.util.Format[column.renderer];
            } else if (column.renderer) {
                try {
                    column.renderer = eval(column.renderer);
                } catch(e) {
                    throw "invalid renderer: "+column.renderer;
                }
            } else if (column.showDataIndex) {
                column.renderer = Ext.util.Format.showField(column.showDataIndex);
            }

            if (column.defaultValue) delete column.defaultValue;
            if (column.dateFormat) delete column.dateFormat;
            if (typeof column.sortable == 'undefined') column.sortable = meta.sortable;
            config.push(column);
        }
        gridConfig.colModel = new Ext.grid.ColumnModel(config);

        this.gridConfig.listeners.validateedit = function(e) {
            this.comboBoxes.each(function(box) {
                if(e.field == box.column.dataIndex && box.column.showDataIndex) {
                    e.record.data[box.column.showDataIndex] = box.field.getRawValue();
                }
            }, this);
        };


        /* * Für DD
        var ddrow = new Ext.dd.DropTarget(this.grid.container, {
            ddGroup : 'GridDD',
            copy:false,
            notifyDrop : function(dd, e, data){
                var sm=data.grid.getSelectionModel();
                var rows=sm.getSelections();
                ds = data.grid.getDataSource();

                var cindex=dd.getDragData(e).rowIndex;
                for (i = 0; i < rows.length; i++) {
                    rowData=ds.getById(rows[i].id);
                    if(!this.copy) {
                        ds.remove(ds.getById(rows[i].id));
                        ds.insert(cindex,rowData);
                    }
                };
            },
            scope:this
        });
        */

        if (meta.paging) {
            if (typeof meta.paging == 'object') {
                var t;
                if (meta.paging.type && Vps.PagingToolbar[meta.paging.type]) {
                    this.pagingType = meta.paging.type;
                    t = Vps.PagingToolbar[meta.paging.type];
                } else if(meta.paging.type) {
                    try {
                        t = eval(meta.paging.type);
                    } catch(e) {
                        throw "invalid paging-toolbar: "+meta.paging.type;
                    }
                    this.pagingType = meta.paging.type;
                } else {
                    this.pagingType = 'Ext.PagingToolbar';
                    t = Ext.PagingToolbar;
                }
                delete meta.paging.type;
                var pagingConfig = meta.paging;
                pagingConfig.store = this.store;
                gridConfig.bbar = new t(pagingConfig);
            } else {
                this.pagingType = 'Ext.PagingToolbar';
                gridConfig.bbar = new Ext.PagingToolbar({
                        store: this.store,
                        pageSize: meta.paging,
                        displayInfo: true
                    });
            }
        } else {
            this.pagingType = false;
        }

        if (meta.buttons.reload) {
            gridConfig.tbar.add(this.getAction('reload'));
        }
        if (meta.buttons.save) {
            gridConfig.tbar.add(this.getAction('save'));
            gridConfig.tbar.add('-');
        }
        if (meta.buttons.add) {
            gridConfig.tbar.add(this.getAction('add'));
        }
        if (meta.buttons['delete']) {
            gridConfig.tbar.add(this.getAction('delete'));
        }

        var filtersEmpty = true;
        for (var i in meta.filters) filtersEmpty = false; //durch was einfacheres ersetzen :D
        if (!filtersEmpty) {
            if(gridConfig.tbar.length > 0) {
                gridConfig.tbar.add('-');
            }
            gridConfig.tbar.add('Filter:');
        }

        if (meta.filters.text) {
            var textfield = new Ext.form.TextField();
            gridConfig.tbar.add(textfield);
            textfield.on('render', function(textfield) {
                textfield.getEl().on('keypress', function() {
                    this.applyBaseParams({query: textfield.getValue()});
                    this.load();
                }, this, {buffer: 500});
            }, this);
            delete meta.filters.text;
            this.filters.push(textfield);
        }

        for(var filter in meta.filters) {
            if (meta.filters[filter].type == 'ComboBox') {
                var data = meta.filters[filter].data;
                data.unshift([0, 'alle']);
                var filterStore = new Ext.data.SimpleStore({
                    id: 0,
                    fields: ['id', 'name'],
                    data: data
                });
                var combo = new Ext.form.ComboBox({
                        store: filterStore,
                        displayField: 'name',
                        valueField: 'id',
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false,
                        width: 200
                    });
                combo.setValue(0);
                gridConfig.tbar.add(' ');
                gridConfig.tbar.add(combo);
                combo.on('select', function(combo, record, index) {
                    var params = {};
                    params['query_'+filter] = record.id;
                    this.applyBaseParams(params);
                    this.load();
                }, this);
                this.filters.push(combo);
            } else if (meta.filters[filter].type == 'DateRange') {
                var fieldFrom = new Vps.Form.DateField({
                    width: 80,
                    value: meta.filters[filter].from
                });
                var fieldTo = new Vps.Form.DateField({
                    width: 80,
                    value: meta.filters[filter].to
                });
                gridConfig.tbar.add(fieldFrom);
                gridConfig.tbar.add(' - ');
                gridConfig.tbar.add(fieldTo);
                gridConfig.tbar.add(new Ext.Button({
                    text: '»',
                    handler: function() {
                        var params = {};
                        params[filter+'_from'] = fieldFrom.getValue().format('Y-m-d');
                        params[filter+'_to'] = fieldTo.getValue().format('Y-m-d');
                        this.load();
                    },
                    scope: this
                }));
                //todo: this.filters.push(textfield);
            }
        }

        if (meta.buttons.pdf || meta.buttons.csv || meta.buttons.xls) {
            gridConfig.tbar.add('->');
        }
        if (meta.buttons.pdf) {
            gridConfig.tbar.add(this.getAction('pdf'));
        }
        if (meta.buttons.csv) {
            gridConfig.tbar.add(this.getAction('csv'));
        }
        if (meta.buttons.xls) {
            gridConfig.tbar.add(this.getAction('xls'));
        }

        //editDialog kann entweder von config übergeben werden oder von meta-daten kommen
        if (!this.editDialog && meta.editDialog) {
            this.editDialog = meta.editDialog;
        }
        if (this.editDialog && !(this.editDialog instanceof Ext.Window)) {
            this.editDialog = new Vps.Auto.Form.Window(meta.editDialog);
        }
        if (this.editDialog) {

            this.editDialog.on('datachange', function() {
                this.reload();
            }, this);

            if (this.editDialog.allowEdit !== false) {
                this.on('rowdblclick', function(grid, rowIndex) {
                    this.editDialog.showEdit(this.store.getAt(rowIndex).id);
                }, this);
            }
        }

        //wenn toolbar leer und keine tbar über config gesetzt dann nicht erstellen
        if (gridConfig.tbar.length == 0 && (!this.initialConfig.gridConfig ||
                                            !this.initialConfig.gridConfig.tbar)) {
            delete gridConfig.tbar;
        }

        this.grid = new Ext.grid.EditorGridPanel(gridConfig);

        this.fireEvent('beforerendergrid', this.grid);

        this.add(this.grid);
        this.doLayout();

        this.fireEvent('rendergrid', this.grid);

        this.relayEvents(this.grid, ['rowdblclick']);

        if (result.rows) {
            this.store.loadData(result);
        }
    },

    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'reload') {
            this.actions[type] = new Ext.Action({
                text    : '',
                handler : this.reload,
                icon    : '/assets/vps/images/silkicons/bullet_star.png',
                cls     : 'x-btn-icon',
                scope   : this
            });
        } else if (type == 'save') {
            this.actions[type] = new Ext.Action({
                text    : 'Save',
                icon    : '/assets/vps/images/silkicons/table_save.png',
                cls     : 'x-btn-text-icon',
                disabled: true, //?? passt des?
                handler : this.onSave,
                scope   : this
            });
        } else if (type == 'add') {
            this.actions[type] = new Ext.Action({
                text    : 'Add',
                icon    : '/assets/vps/images/silkicons/table_add.png',
                cls     : 'x-btn-text-icon',
                handler : this.onAdd,
                scope: this
            });
        } else if (type == 'delete') {
            this.actions[type] = new Ext.Action({
                text    : 'Delete',
                icon    : '/assets/vps/images/silkicons/table_delete.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                handler : this.onDelete,
                scope: this
            });
        } else if (type == 'pdf') {
            this.actions[type] = new Ext.Action({
                text    : 'Drucken',
                icon    : '/assets/vps/images/silkicons/printer.png',
                cls     : 'x-btn-text-icon',
                handler : this.onPdf,
                scope: this
            });
        } else if (type == 'csv') {
            this.actions[type] = new Ext.Action({
                text    : 'CSV Export',
                icon    : '/assets/vps/images/silkicons/page_code.png',
                cls     : 'x-btn-text-icon',
                handler : this.onCsv,
                scope: this
            });
        } else if (type == 'xls') {
            this.actions[type] = new Ext.Action({
                text    : 'Excel Export',
                icon    : '/assets/vps/images/silkicons/page_excel.png',
                cls     : 'x-btn-text-icon',
                handler : this.onXls,
                scope: this
            });
        } else {
            throw "unknown action-type: "+type;
        }
        return this.actions[type];
    },

    //protected, zum überschreiben in unterklassen um zusäztliche daten zu speichern
    getSaveParams : function()
    {
        var data = [];
        var modified = this.store.getModifiedRecords();
        if (!modified.length) return {};
        //geänderte records
        modified.each(function(r) {
            this.store.newRecords.remove(r); //nur einmal speichern
            data.push(r.data);
        }, this);

        //neue, ungeänderte records
        this.store.newRecords.each(function(r) {
            data.push(r.data);
        }, this);

        this.el.mask('Saving...');

        var params = this.baseParams || {};
        params.data = Ext.util.JSON.encode(data);
        return params;
    },
    onSave : function()
    {
        this.submit();
    },
    submit : function(callback, addParams)
    {
        this.getAction('save').disable();
        var params = this.getSaveParams();
        Ext.apply(params, addParams);

        if (params == {}) return;

        Ext.Ajax.request({
            url: this.controllerUrl+'jsonSave',
            params: params,
            success: function(response, options, r) {
                this.reload();
                if (callback && callback.callback) {
                    callback.callback.call(callback.scope||this, response, options, r);
                }
            },
            failure: function() {
                this.getAction('save').enable();
            },
            callback: function() {
                this.el.unmask();
            },
            scope  : this
        });
    },

    onAdd : function()
    {
        if (this.editDialog) {
            this.editDialog.showAdd();
        } else {
            var data = {};
            for(var i=0; i<this.store.recordType.prototype.fields.items.length; i++) {
                data[this.store.recordType.prototype.fields.items[i].name] = this.store.recordType.prototype.fields.items[i].defaultValue;
            }
            var record = new this.store.recordType(data);

            this.getGrid().stopEditing();
            this.store.insert(0, record);
            this.store.newRecords.push(record);

            for(var i=0; i<this.getGrid().getColumnModel().getColumnCount(); i++) {
                if(!this.getGrid().getColumnModel().isHidden(i) && this.getGrid().getColumnModel().isCellEditable(i, 0)) {
                    this.getGrid().startEditing(0, i);
                    break;
                }
            }
        }
    },

    onDelete : function() {
        Ext.Msg.show({
            title:'Löschen',
            msg: 'Möchten Sie diesen Eintrag wirklich löschen?',
            buttons: Ext.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    var selectedRow = this.getGrid().getSelectionModel().getSelected();
                    if (!selectedRow) return;
                    if (selectedRow.data.id == 0) {
                        this.store.remove(selectedRow);
                    } else {
                        this.el.mask('Deleting...');

                        var params = {};
                        params[this.store.reader.meta.id] = selectedRow.id;
                        Ext.Ajax.request({
                            url: this.controllerUrl+'jsonDelete',
                            params: params,
                            success: function() {
                                this.reload();
                                this.getAction('delete').disable();
                                this.fireEvent('deleterow', this.grid);
                            },
                            failure: function() {
                                this.getAction('delete').enable();
                            },
                            callback: function() {
                                this.el.unmask();
                            },
                            scope : this
                        });
                    }
                }
            }
        });
    },
    onPdf : function()
    {
        window.open(this.controllerUrl+'pdf?'+Ext.urlEncode(this.baseParams));
    },
    onCsv : function()
    {
        window.open(this.controllerUrl+'csv?'+Ext.urlEncode(this.baseParams));
    },
    onXls : function()
    {
        window.open(this.controllerUrl+'xls?'+Ext.urlEncode(this.baseParams));
    },
    getSelected: function() {
        return this.getSelectionModel().getSelected();
    },
    clearSelections: function() {
        this.getGrid().getSelectionModel().clearSelections();
    },
    selectRow: function(row) {
        this.getSelectionModel().selectRow(row);
    },
    reload: function(options) {
        this.store.reload(options);
        this.store.commitChanges();
    },
    load : function(params) {
        if (!params) params = {};
        if (this.pagingType && this.pagingType != 'Date' && !params.start) {
            params.start = 0;
        }
        this.getStore().load({ params: params });
    },
    getGrid : function() {
        return this.grid;
    },
    getSelectionModel : function() {
        return this.getGrid().getSelectionModel();
    },
    getColumnModel : function() {
        return this.getGrid().getColumnModel();
    },
    getStore : function() {
        return this.store;
    },
    getEditDialog : function() {
        return this.editDialog;
    },
    setBaseParams : function(baseParams) {
        if (this.editDialog) {
            this.editDialog.setBaseParams(baseParams);
        }
        this.getStore().baseParams = baseParams;
    },
    applyBaseParams : function(baseParams) {
        if (this.editDialog) {
            this.editDialog.applyBaseParams(baseParams);
        }
        Ext.apply(this.getStore().baseParams, baseParams);
    },
    resetFilters: function() {
        if (this.getStore().baseParams.query) delete this.getStore().baseParams.query;
        this.filters.each(function(f) {
            f.setValue(f.defaultValue || '');
            
        }, this);
    }
});

Ext.reg('autogrid', Vps.Auto.GridPanel);
