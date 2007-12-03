Vps.Auto.GridPanel = Ext.extend(Vps.Auto.AbstractPanel,
{
    controllerUrl: '',
    //autoload: true,
    layout: 'fit',

    initComponent : function()
    {
        this.actions = {};

        if (!this.gridConfig) this.gridConfig = { plugins: [] };

//         if(this.autoload) {
        //todo: wos bosiat bei !autoload
            if (!this.controllerUrl) {
                throw new Error('No controllerUrl specified for AutoGrid.');
            }
            Ext.Ajax.request({
                mask: true,
                url: this.controllerUrl+'/jsonData',
                params: Ext.apply({ meta: true }, this.baseParams),
                success: function(response, options, r) {
                    var result = Ext.decode(response.responseText);
                    this.onMetaLoad(result);
                },
                scope: this
            });
//         }

        this.addEvents(
            'rendergrid',
            'beforerendergrid',
            'deleterow'
        );

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
                proxy: new Ext.data.HttpProxy({ url: this.controllerUrl + '/jsonData' }),
                reader: new Ext.data.JsonReader({
                    totalProperty: meta.totalProperty,
                    root: meta.root,
                    id: meta.id,
                    sucessProperty: meta.successProperty,
                    fields: meta.fields
                }),
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
            if (this.baseParams) storeConfig.baseParams = this.baseParams;
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

        var gridConfig = Ext.applyIf(this.gridConfig, {
            store: this.store,
            selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
            clicksToEdit: 1,
            border: false,
            loadMask: true,
            plugins: [],
            tbar: [],
            listeners: { scope: this }
        });

        this.relayEvents(this.store, ['load']);
        this.relayEvents(gridConfig.selModel, ['selectionchange', 'rowselect', 'beforerowselect']);

        gridConfig.selModel.on('rowselect', function(selData, gridRow, currentRow) {
            this.getAction('delete').enable();
        }, this);

        gridConfig.selModel.on('beforerowselect', function(selModel, rowIndex, keepExisting, record) {
            return this.fireEvent('beforeselectionchange', record.id);
        }, this);

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

        var config = this.gridConfig.colModel || [];
        if (Ext.grid.CheckboxSelectionModel && this.gridConfig.selModel instanceof Ext.grid.CheckboxSelectionModel) {
            config.push(this.gridConfig.selModel);
        }
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
        if (config instanceof Array) {
            gridConfig.colModel = new Ext.grid.ColumnModel(config);
        } else {
            gridConfig.colModel = config;
        }

        this.gridConfig.listeners.validateedit = function(e) {
            this.comboBoxes.each(function(box) {
                if(e.field == box.column.dataIndex && box.column.showDataIndex) {
                    e.record.data[box.column.showDataIndex] = box.field.getRawValue();
                }
            }, this);
        };


        //editDialog kann entweder von config übergeben werden oder von meta-daten kommen
        if (!this.editDialog && meta.editDialog) {
            this.editDialog = meta.editDialog;
        }
        if (typeof this.editDialog == "string") {
            try {
                var d = eval(this.editDialog);
            } catch (e) {
                throw new Error("Invalid editDialog \'"+this.editDialog+"': "+e);
            }
            this.editDialog = new d({ baseCls: 'x-plain' });
        }
        if (this.editDialog instanceof Vps.Auto.FormPanel) {
            this.editDialog = new Vps.Auto.Form.Window({ autoForm: this.editDialog });
        }
        if (this.editDialog && !(this.editDialog instanceof Ext.Window)) {
            this.editDialog = new Vps.Auto.Form.Window(this.editDialog);
        }
        if (this.editDialog) {
            this.editDialog.on('datachange', function(r) {
                this.reload();
                //r nicht durchschleifen - weil das probleme verursacht wenn
                //das grid zB an einem Tree gebunden ist
                this.fireEvent('datachange');
            }, this);

            if (this.editDialog.allowEdit !== false) {
                this.on('rowdblclick', function(grid, rowIndex) {
                    this.editDialog.showEdit(this.store.getAt(rowIndex).id);
                }, this);
            }
        }

        for (var i in this.actions) {
            if (i == 'add' && this.editDialog) continue; //add-button anzeigen auch wenn keine permissions da die add-permissions im dialog sein müssen
            if (!meta.permissions[i]) {
                this.getAction(i).hide();
            }
        }
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
            delete meta.buttons.reload;
        }
        if (meta.buttons.save) {
            gridConfig.tbar.add(this.getAction('save'));
            gridConfig.tbar.add('-');
            delete meta.buttons.save;
        }
        if (meta.buttons.add) {
            gridConfig.tbar.add(this.getAction('add'));
            delete meta.buttons.add;
        }
        if (meta.buttons['delete']) {
            gridConfig.tbar.add(this.getAction('delete'));
            delete meta.buttons['delete'];
        }
        for (var i in meta.buttons) {
            if (i != 'pdf' && i != 'csv' && i != 'xls') {
                gridConfig.tbar.add(this.getAction(i));
            }
        }

        var filtersEmpty = true;
        for (var i in meta.filters) filtersEmpty = false; //durch was einfacheres ersetzen :D
        if (!filtersEmpty) {
            if(gridConfig.tbar.length > 0) {
                gridConfig.tbar.add('-');
            }
            gridConfig.tbar.add('Filter:');
        }
        if (meta.filters.text && typeof(meta.filters.text) != 'object') {
            meta.filters.text = { type: 'TextField' };
        }
        this.filters = new Ext.util.MixedCollection();
        var first = true;
        for(var filter in meta.filters) {
            var f = meta.filters[filter]
            if (!Vps.Auto.GridFilter[f.type]) {
                throw "Unknown filter.type: "+f.type;
            }
            var type = Vps.Auto.GridFilter[f.type];
            delete f.type;
            f.id = filter;
            f = new type(f);
            if (!first) gridConfig.tbar.add('  ');
            f.getToolbarItem().each(function(i) {
                gridConfig.tbar.add(i);
            });
            this.filters.add(f);
            f.on('filter', function(f, params) {
                this.applyBaseParams(params);
                this.load();
            }, this);
            first = false;
        }

        if (meta.buttons.pdf || meta.buttons.xls || meta.buttons.csv) {
            gridConfig.tbar.add('->');
        }
        if (meta.buttons.pdf) {
            gridConfig.tbar.add(this.getAction('pdf'));
        }
        if (meta.buttons.xls) {
            gridConfig.tbar.add(this.getAction('xls'));
        }
        if (meta.buttons.csv) {
            gridConfig.tbar.add(this.getAction('csv'));
        }
        for (var i = 0; i < gridConfig.tbar.length; i++) {
            if (typeof gridConfig.tbar[i] == 'string'
                    && this.getAction(gridConfig.tbar[i])) {
                gridConfig.tbar[i] = this.getAction(gridConfig.tbar[i]);
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
                icon    : '/assets/silkicons/bullet_star.png',
                cls     : 'x-btn-icon',
                scope   : this
            });
        } else if (type == 'save') {
            this.actions[type] = new Ext.Action({
                text    : 'Save',
                icon    : '/assets/silkicons/table_save.png',
                cls     : 'x-btn-text-icon',
                disabled: true, //?? passt des?
                handler : this.onSave,
                scope   : this
            });
        } else if (type == 'add') {
            this.actions[type] = new Ext.Action({
                text    : 'Add',
                icon    : '/assets/silkicons/table_add.png',
                cls     : 'x-btn-text-icon',
                handler : this.onAdd,
                scope: this
            });
        } else if (type == 'delete') {
            this.actions[type] = new Ext.Action({
                text    : 'Delete',
                icon    : '/assets/silkicons/table_delete.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                handler : this.onDelete,
                scope: this
            });
        } else if (type == 'pdf') {
            this.actions[type] = new Ext.Action({
                text    : 'Drucken',
                icon    : '/assets/silkicons/printer.png',
                cls     : 'x-btn-text-icon',
                handler : this.onPdf,
                scope: this
            });
        } else if (type == 'csv') {
            this.actions[type] = new Ext.Action({
                text    : 'CSV Export',
                icon    : '/assets/silkicons/page_code.png',
                cls     : 'x-btn-text-icon',
                handler : this.onCsv,
                scope: this
            });
        } else if (type == 'xls') {
            this.actions[type] = new Ext.Action({
                text    : 'Excel Export',
                icon    : '/assets/silkicons/page_excel.png',
                cls     : 'x-btn-text-icon',
                handler : this.onXls,
                scope: this
            });
        } else {
            return null;
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

        var params = this.getBaseParams() || {};
        params.data = Ext.util.JSON.encode(data);
        return params;
    },
    onSave : function()
    {
        this.submit();
    },
    submit : function(options)
    {
        if (!options) options = {};

        if (arguments[1]) options.params = arguments[1]; //backwards compatibility

        this.getAction('save').disable();
        var params = this.getSaveParams();
        if (options.params) Ext.apply(params, options.params);

        if (params == {}) return;

        var cb = {
            success: options.success,
            failure: options.failulre,
            callback: options.callback,
            scope: options.scope || this
        };

        Ext.Ajax.request({
            url: this.controllerUrl+'/jsonSave',
            params: params,
            success: function(response, options, r) {
                this.reload();
                this.fireEvent('datachange', r);
                if (cb.success) {
                    cb.success.apply(cb.scope, arguments)
                }
            },
            failure: function() {
                this.getAction('save').enable();
                if (cb.failure) {
                    cb.failure.apply(cb.scope, arguments)
                }
            },
            callback: function() {
                this.el.unmask();
                if (cb.callback) {
                    cb.callback.apply(cb.scope, arguments)
                }
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
            msg: 'Do you really wish to remove this entry / these entries?',
            buttons: Ext.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    var selectedRows = this.getGrid().getSelectionModel().getSelections();
                    if (selectedRows.length == 0) return;

                    var ids = [];
                    var params = {};
                    selectedRows.each(function(selectedRow)
                    {
                        if (selectedRow.data.id == 0) {
                            this.store.remove(selectedRow);
                        } else {
                            if (!params[this.store.reader.meta.id]) {
                                params[this.store.reader.meta.id] = '';
                            } else {
                                params[this.store.reader.meta.id] += ';';
                            }
                            params[this.store.reader.meta.id] += selectedRow.id;
                        }
                    }, this);

                    if (params[this.store.reader.meta.id]) {
                        this.el.mask('Deleting...');
                        Ext.Ajax.request({
                            url: this.controllerUrl+'/jsonDelete',
                            params: params,
                            success: function(response, options, r) {
                                this.reload();
                                this.getAction('delete').disable();
                                this.fireEvent('deleterow', this.grid);
                                this.fireEvent('datachange', r);
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
        window.open(this.controllerUrl+'/pdf?'+Ext.urlEncode(this.getStore().baseParams));
    },
    onCsv : function()
    {
        window.open(this.controllerUrl+'/csv?'+Ext.urlEncode(this.getStore().baseParams));
    },
    onXls : function()
    {
        window.open(this.controllerUrl+'/xls?'+Ext.urlEncode(this.getStore().baseParams));
    },
    getSelected: function() {
        return this.getSelectionModel().getSelected();
    },

    //für AbstractPanel
    getSelectedId: function() {
        var s = this.getSelected();
        if (s) return s.id;
        return null;
    },
    clearSelections: function() {
        this.getGrid().getSelectionModel().clearSelections();
    },
    selectRow: function(row) {
        this.getSelectionModel().selectRow(row);
    },

    //für AbstractPanel
    selectId: function(id) {
        if (id) {
            var r = this.getStore().getById(id);
            if (r) {
                this.getSelectionModel().selectRecords([r]);
            }
        } else {
            this.getSelectionModel().clearSelections();
        }
    },

    //für AbstractPanel
    reset: function() {
        this.getStore().modified = [];
        this.store.newRecords = [];
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
    getBaseParams : function() {
        return this.getStore().baseParams;
    },
    setBaseParams : function(baseParams) {
        if (this.editDialog) {
            this.editDialog.getAutoForm().setBaseParams(baseParams);
        }
        this.getStore().baseParams = baseParams;
    },
    applyBaseParams : function(baseParams) {
        if (this.editDialog) {
            this.editDialog.getAutoForm().applyBaseParams(baseParams);
        }
        Ext.apply(this.getStore().baseParams, baseParams);
    },
    resetFilters: function() {
        this.filters.each(function(f) {
            f.reset();
            this.applyBaseParams(f.getParams());
        }, this);
    },
    isDirty: function() {
        if (this.store.getModifiedRecords().length || this.store.newRecords.legth) {
            return true;
        } else {
            return false;
        }
    }
});

Ext.reg('autogrid', Vps.Auto.GridPanel);
