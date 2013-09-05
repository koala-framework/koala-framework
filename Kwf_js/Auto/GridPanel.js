Kwf.Auto.GridPanel = Ext.extend(Kwf.Binding.AbstractPanel,
{
    layout: 'fit',

    // true, false, integer. If integer: after x filters another new tbar is generated
    filtersInSeparateTbar: false,

    initComponent : function()
    {
        if (!this.gridConfig) this.gridConfig = { plugins: [] };
        if (this.autoLoad !== false) {
            this.autoLoad = true;
        } else {
            delete this.autoLoad;
        }

        this.addEvents(
            'rendergrid',
            'beforerendergrid',
            'deleterow',
            'cellclick',
            'celldblclick',
            'rowdblclick'
        );

        this.actions.reload = new Ext.Action({
            icon    : '/assets/silkicons/arrow_rotate_clockwise.png',
            cls     : 'x-btn-icon',
            tooltip : trlKwf('Reload'),
            handler : this.reload,
            scope   : this
        });
        this.actions.save = new Ext.Action({
            text    : trlKwf('Save'),
            icon    : '/assets/silkicons/table_save.png',
            cls     : 'x-btn-text-icon',
            disabled: true,
            handler : this.onSave,
            scope   : this
        });
        this.actions.add = new Ext.Action({
            text    : trlKwf('Add'),
            icon    : '/assets/silkicons/table_add.png',
            cls     : 'x-btn-text-icon',
            handler : this.onAdd,
            scope: this
        });
        this.actions['delete'] = new Ext.Action({
            text    : trlKwf('Delete'),
            icon    : '/assets/silkicons/table_delete.png',
            cls     : 'x-btn-text-icon',
            handler : this.onDelete,
            scope: this,
            needsSelection: true
        });
        this.actions.edit = new Ext.Action({
            text    : trlKwf('Edit'),
            icon    : '/assets/silkicons/table_edit.png',
            cls     : 'x-btn-text-icon',
            handler : this.onEdit,
            scope: this,
            needsSelection: true
        });
        this.actions.duplicate = new Ext.Action({
            text    : trlKwf('Duplicate'),
            icon    : '/assets/silkicons/table_go.png',
            cls     : 'x-btn-text-icon',
            handler : this.onDuplicate,
            scope: this,
            needsSelection: true
        });
        this.actions.pdf = new Ext.Action({
            text    : trlKwf('Print'),
            icon    : '/assets/silkicons/printer.png',
            cls     : 'x-btn-text-icon',
            handler : this.onPdf,
            scope: this
        });
        this.actions.csv = new Ext.Action({
            text    : trlKwf('CSV Export'),
            icon    : '/assets/silkicons/page_code.png',
            cls     : 'x-btn-text-icon',
            handler : this.onCsv,
            scope: this
        });
        this.actions.xls = new Ext.Action({
            text    : trlKwf('Excel Export'),
            icon    : '/assets/silkicons/page_excel.png',
            cls     : 'x-btn-text-icon',
            handler : this.onXls,
            scope: this
        });
        Kwf.Auto.GridPanel.superclass.initComponent.call(this);
    },

    doAutoLoad : function()
    {
        //autoLoad kann in der zwischenzeit abgeschaltet werden, zB wenn
        //wir in einem Binding sind
        if (!this.autoLoad) return;
        if (typeof this.initialConfig.autoLoadId != 'undefined' && this.initialConfig.autoLoadId) {
            this.autoLoadIdLoaded = false;
            this.applyBaseParams({ query: 'id:'+this.initialConfig.autoLoadId });
            this.load();
            this.on('load', function() {
                if (this.getFilter('text') && !this.autoLoadIdLoaded) {
                    this.getFilter('text').textField.setValue('id:'+this.initialConfig.autoLoadId);
                    this.selectId.defer(1, this, [ this.initialConfig.autoLoadId ]);
                }
                this.autoLoadIdLoaded = true;
            }, this);
        } else {
            this.load();
        }
    },

    onMetaLoad : function(result)
    {
        var meta = result.metaData;
        this.metaData = meta;

        if (!this.store) {
            var remoteSort = false;
            if (meta.paging) remoteSort = true;
            if (!meta.sortable) remoteSort = true;
            var storeConfig = {
                proxy: new Ext.data.HttpProxy({ url: this.controllerUrl + '/json-data' }),
                reader: new Ext.data.JsonReader({
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
            if (meta.grouping) {
                var storeType = Ext.data.GroupingStore;
                storeConfig.groupField = meta.grouping.groupField;
                delete meta.grouping.groupField;
            } else {
                var storeType = Ext.data.Store;
            }
            this.store = new storeType(storeConfig);
            if (this.baseParams) {
                this.setBaseParams(this.baseParams);
                delete this.baseParams;
            }
        }

        this.store.newRecords = []; //hier werden neue records gespeichert die nicht dirty sind
        this.store.on('update', function(store, record, operation) {
            if (operation == Ext.data.Record.EDIT) {
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

        var alwaysKeepTbar = (typeof this.gridConfig.tbar != 'undefined');

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

        gridConfig.selModel.on('selectionchange', function() {
            if (this.getSelected()) {
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

        gridConfig.selModel.on('beforerowselect', function(selModel, rowIndex, keepExisting, record) {
            return this.fireEvent('beforeselectionchange', record.id);
        }, this);

        if (meta.grouping) {
            if (!gridConfig.view) {
                gridConfig.view = new Ext.grid.GroupingView(Ext.applyIf(meta.grouping,
                {}));
            }

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
        } else if (!gridConfig.view) {
            gridConfig.view = new Ext.grid.GridView();
        }

        this.comboBoxes = [];

        var config = this.gridConfig.colModel || [];
        if (Ext.grid.CheckboxSelectionModel && this.gridConfig.selModel instanceof Ext.grid.CheckboxSelectionModel) {
            config.push(this.gridConfig.selModel);
        }
        for (var i=0; i<meta.columns.length; i++) {
            var column = meta.columns[i];
            if (column.header == null) continue;

            if (column['class'] && column['class'] != '') {
                var cl = eval(column['class']);
                column = new cl(Ext.apply({'header' : column.header}, column.config));
            } else if (typeof column.renderer == 'function') {
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
            } else {
                column.renderer = Ext.util.Format.htmlEncode;
            }
            if (column.summaryRenderer) {
                if (Ext.util.Format[column.summaryRenderer]) {
                    column.summaryRenderer = Ext.util.Format[column.summaryRenderer];
                } else {
                    try {
                        column.summaryRenderer = eval(column.summaryRenderer);
                    } catch(e) {
                        throw "invalid summaryRenderer: "+column.summaryRenderer;
                    }
                }
            }

            if (column.editor && column.editor.xtype == 'checkbox') {
                delete column.editor;
                column = new Ext.grid.CheckColumn(column);
                gridConfig.plugins.push(column);
            } else if (column.editor) {
                Ext.applyIf(column.editor, { msgTarget: 'qtip' });
                var editorField = Ext.ComponentMgr.create(column.editor, 'textfield');
                var editorConfig = {};
                if(editorField instanceof Ext.form.ComboBox) {
                    this.comboBoxes.push({
                        field: editorField,
                        column: column
                    });
                } else if (Kwf.Form.AbstractSelect && editorField instanceof Kwf.Form.AbstractSelect) {
                    editorConfig.allowBlur = true;
                }
                column.editor = new Ext.grid.GridEditor(editorField, editorConfig);
            }

            if (column.columnType == 'button') {
                if (column.editDialog) {
                    column.editDialog = this.initEditDialog(column.editDialog);
                }
                column.clickHandler = function(grid, rowIndex, col, e) {
                    var r = grid.getStore().getAt(rowIndex);
                    if (col.editDialog) {
                        col.editDialog.showEdit(r.id, r);
                    } else {
                        this.edit(r);
                    }
                };
            }

            if (column.defaultValue) delete column.defaultValue;
            if (column.dateFormat) delete column.dateFormat;
            if (typeof column.sortable == 'undefined') column.sortable = meta.sortable;

            if (this.columnsConfig && this.columnsConfig[column.dataIndex]) {
                Ext.apply(column, this.columnsConfig[column.dataIndex]);
            }
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
        if (this.editDialog) {
            this.editDialog = this.initEditDialog(this.editDialog);
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

        for (var i in this.actions) {
            if (this.actions[i].initialConfig.needsSelection) {
                this.actions[i].disable();
            }
            if (i == 'add' && this.editDialog) continue; //add-button anzeigen auch wenn keine permissions da die add-permissions im dialog sein müssen
            if (!meta.permissions[i] && this.getAction(i).hide) {
                this.getAction(i).hide();
            }
        }

        //aktionen die als string in der tbar sind
        var existingActions = {};
        for (var i = 0; i < gridConfig.tbar.length; i++) {
            if (typeof gridConfig.tbar[i] == 'string'
                    && this.getAction(gridConfig.tbar[i])) {
                existingActions[gridConfig.tbar[i]] = true;
            }
        }


        if (meta.buttons.save) {
            if (!existingActions.save) {
                gridConfig.tbar.add(this.getAction('save'));
                gridConfig.tbar.add('-');
            }
            delete meta.buttons.save;
        }
        if (meta.buttons.edit) {
            if (!existingActions.edit) {
                gridConfig.tbar.add(this.getAction('edit'));
            }
            delete meta.buttons.edit;
        }
        if (meta.buttons.add) {
            if (!existingActions.add) {
                gridConfig.tbar.add(this.getAction('add'));
            }
            delete meta.buttons.add;
        }
        if (meta.buttons['delete']) {
            if (!existingActions['delete']) {
                gridConfig.tbar.add(this.getAction('delete'));
            }
            delete meta.buttons['delete'];
        }
        if (meta.buttons.duplicate) {
            if (!existingActions.duplicate) {
                gridConfig.tbar.add(this.getAction('duplicate'));
            }
            delete meta.buttons.duplicate;
        }

        for (var i in meta.buttons) {
            if (i != 'pdf' && i != 'csv' && i != 'xls' && i != 'reload' && !existingActions[i]) {
                if (this.getAction(i)) {
                    gridConfig.tbar.add(this.getAction(i));
                } else {
                    gridConfig.tbar.add(i);
                }
            }
        }

        for (var i = 0; i < gridConfig.tbar.length; i++) {
            if (typeof gridConfig.tbar[i] == 'string'
                    && this.getAction(gridConfig.tbar[i])) {
                gridConfig.tbar[i] = this.getAction(gridConfig.tbar[i]);
            }
        }

        this.filters = new Kwf.Auto.FilterCollection(meta.filters, this);
        this.filters.each(function(filter) {
            filter.on('filter', function(f, params) {
                this.applyBaseParams(params);
                this.load();
            }, this);
        }, this);

        if (this.filtersInSeparateTbar === false || (
            typeof this.filtersInSeparateTbar != 'boolean' && this.filtersInSeparateTbar >= 1
        )) {
            if (this.filtersInSeparateTbar) {
                this.filters.applyToTbar(gridConfig.tbar, this.filtersInSeparateTbar);
            } else {
                this.filters.applyToTbar(gridConfig.tbar);
            }
        }

        if (meta.buttons.pdf || meta.buttons.xls || meta.buttons.csv || meta.buttons.reload) {
            gridConfig.tbar.add('->');
        }
        if (meta.buttons.pdf && !existingActions.pdf) {
            gridConfig.tbar.add(this.getAction('pdf'));
        }
        if (meta.buttons.xls && !existingActions.xls) {
            gridConfig.tbar.add(this.getAction('xls'));
        }
        if (meta.buttons.csv && !existingActions.csv) {
            gridConfig.tbar.add(this.getAction('csv'));
        }
        if (meta.buttons.reload && !existingActions.reload) {
            gridConfig.tbar.add(this.getAction('reload'));
        }

        if (meta.helpText) {
            gridConfig.tbar.add('->');
            gridConfig.tbar.add(new Ext.Action({
                icon : '/assets/silkicons/information.png',
                cls : 'x-btn-icon',
                handler : function (a) {
                    var helpWindow = new Ext.Window({
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
        if (gridConfig.tbar.length == 0 && !alwaysKeepTbar) {
            delete gridConfig.tbar;
        }

        gridConfig.filtersInSeparateTbar = this.filtersInSeparateTbar;
        if (this.filtersInSeparateTbar) {
            gridConfig.filters = this.filters;
        }

        this.grid = new Ext.grid.EditorGridPanel(gridConfig);
        this.relayEvents(this.grid, ['beforeedit', 'aftereditcomplete', 'validateedit']);

        this.grid.on('cellclick', function(grid, rowIndex, columnIndex, e) {
            //damit bei doppelclick nur ein event ausgeführt wird
            if (this.ignoreCellClicks) return;
            this.ignoreCellClicks = true;
            (function(){
                this.ignoreCellClicks = false;
            }).defer(500, this);

            this.fireEvent('cellclick', grid, rowIndex, columnIndex, e);
            var col = grid.getColumnModel().config[columnIndex];
            if (col.clickHandler) {
                col.clickHandler.call(col.scope || this, grid, rowIndex, col, e);
            }
        }, this);
        this.grid.on('celldblclick', function(grid, rowIndex, columnIndex, e) {
            //wenn spalte einen eigenen clickhandler hat den dblclick ignorieren
            var col = grid.getColumnModel().config[columnIndex];
            if (!col.clickHandler) {
                this.fireEvent('celldblclick', grid, rowIndex, columnIndex, e);
                this.fireEvent('rowdblclick', grid, rowIndex, e);
                this.edit(this.store.getAt(rowIndex));
            }
        }, this);

        this.fireEvent('beforerendergrid', this.grid);

        //bei renderern zusäztliches argument anhängen (die column)
        //wird nach beforerendergrid gemacht, weil da drinnen können noch eigene
        //renderer angehängt werden.
        this.grid.getColumnModel().config.each(function(column) {
            if (column.renderer) {
                column.renderer = this.createRenderer(column.renderer, column);
            }
        }, this);

        this.add(this.grid);
        this.doLayout();

        this.fireEvent('rendergrid', this.grid);
        this.fireEvent('loaded', this.grid);

        if (result.rows) {
            this.store.loadData(result);
        }
    },

    getFilter : function(filterName) {
        if (this.filters) return this.filters.get(filterName);
    },

    initEditDialog : function(editDialog)
    {
        if (typeof editDialog == "string") {
            try {
                var d = eval(editDialog);
            } catch (e) {
                throw new Error("Invalid editDialog '"+editDialog+"': "+e);
            }
            editDialog = new d({});
        }
        if (editDialog instanceof Kwf.Auto.FormPanel) {
            editDialog = new Kwf.Auto.Form.Window({ autoForm: editDialog });
        }
        if (editDialog && !(editDialog instanceof Ext.Window)) {
            var d = Kwf.Auto.Form.Window;
            if (editDialog.type) {
                try {
                    d = eval(editDialog.type);
                } catch (e) {
                    throw new Error("Invalid editDialog \'"+editDialog.type+"': "+e);
                }
            }
            editDialog = new d(editDialog);
        }
        if (editDialog.applyBaseParams) {
            editDialog.applyBaseParams(this.getBaseParams());
        }
        editDialog.on('datachange', function(r) {
            this.reload();
            //r nicht durchschleifen - weil das probleme verursacht wenn
            //das grid zB an einem Tree gebunden ist
            this.fireEvent('datachange');
        }, this);
        return editDialog;
    },

    //add additional argument to renderer (column)
    createRenderer : function(renderer, column) {
        return function() {
            var a = arguments;
            Array.prototype.push.call(a, column);
            return renderer.apply(this, a);
        };
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

        this.el.mask(trlKwf('Saving...'));

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
        this.grid.stopEditing(false); // blurs all open editor fields (when isset "allowBlur" (e.g. AbstractSelect))

        if (!options) options = {};

        if (arguments[1]) options.params = arguments[1]; //backwards compatibility

        this.getAction('save').disable();

        var params = this.getSaveParams();
        if (options.params) Ext.apply(params, options.params);

        //gibts da keine bessere l�sung?
        var empty = true;
        for (var i in params) {
            empty = false;
            break;
        }
        if (empty) return;

        var cb = {
            success: options.success,
            failure: options.failulre,
            callback: options.callback,
            scope: options.scope || this
        };

        Ext.Ajax.request({
            url: this.controllerUrl+'/json-save',
            params: params,
            success: function(response, options, r) {
                //geänderte und neue zurücksetzen, damit isDirty false ist
                this.store.modified = [];
                this.store.newRecords = [];
                this.reload();
                this.fireEvent('datachange', r);
                if (cb.success) {
                    cb.success.apply(cb.scope, arguments);
                }
            },
            failure: function() {
                this.getAction('save').enable();
                if (cb.failure) {
                    cb.failure.apply(cb.scope, arguments);
                }
            },
            callback: function() {
                this.el.unmask();
                if (cb.callback) {
                    cb.callback.apply(cb.scope, arguments);
                }
            },
            scope  : this
        });
    },
    onEdit : function()
    {
        if (this.getSelected()) {
            this.edit(this.getSelected());
        }
    },

    //protected, wird zB in Paragraphs.Panel überschrieben
    edit : function(row)
    {
        if (this.editDialog && this.editDialog.allowEdit !== false) {
            this.editDialog.showEdit(row.id, row);
        }
    },

    onAdd : function()
    {
        if (this.editDialog) {
            //wenn EditDialog hat diesen öffnen
            this.editDialog.showAdd();
        } else {
            //im ersten form-binding hinzufügen
            var foundForm = false;
            this.bindings.each(function(b) {
                if (b.item.getSupportsAdd()) {
                    b.item.enable();
                    b.item.onAdd();
                    foundForm = true;
                    return false;
                }
            }, this);

            if (!foundForm) {
                //sonst mittels inline-editing einen neuen datensatz anlegen
                var data = {};
                for(var i=0; i<this.store.recordType.prototype.fields.items.length; i++) {
                    data[this.store.recordType.prototype.fields.items[i].name] = this.store.recordType.prototype.fields.items[i].defaultValue;
                }
                var record = new this.store.recordType(data);
                for(var i=0; i<this.getGrid().getColumnModel().getColumnCount(); i++) {
                    if(!this.getGrid().getColumnModel().isHidden(i) && this.getGrid().getColumnModel().isCellEditable(i, 0)) {
                        record.dirty = true;
                        record.modified = {};
                        record.modified[record.fields.items[i].name] = '';
                        break;
                    }
                }

                this.getGrid().stopEditing();

                var rowInsertPosition = 0;
                if (this.insertNewRowAtBottom) {
                    rowInsertPosition = this.store.getCount();
                }

                this.store.insert(rowInsertPosition, record);
                this.store.newRecords.push(record);
                if (record.dirty) {
                    this.getGrid().startEditing(rowInsertPosition, i);
                }

                // check if value for pos-field should be set on add. Only if no
                // paging is enabled and a pos-field is existent
                if (!this.metaData.paging) {
                    for (var i=0; i<this.getGrid().getColumnModel().getColumnCount(); i++) {
                        // Get every cell-editor of the newly added row
                        var cellEditor = this.getGrid().getColumnModel().getCellEditor(i,rowInsertPosition);
                        // Check if cellEditor is a pos-field
                        if (cellEditor && cellEditor.field instanceof Kwf.Form.PosField) {
                            // set value for pos-field
                            record.set(cellEditor.field.name, rowInsertPosition+1);
                            break;
                        }
                    }
                }
            }
        }
        this.fireEvent('addaction', this);
    },

    onDelete : function() {
        Ext.Msg.show({
            title: trlKwf('Delete'),
            msg: trlKwf('Do you really wish to remove this entry / these entries?'),
            buttons: Ext.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    var selectedRows = this.getGrid().getSelectionModel().getSelections();
                    if (!selectedRows.length) return;

                    var ids = [];
                    var params = this.getBaseParams() || {};
                    var newNewRecords = [];
                    selectedRows.each(function(selectedRow)
                    {
                        if (selectedRow.data.id == 0) {
                            this.store.remove(selectedRow);
                            this.store.newRecords.each(function(r) {
                                if (selectedRow != r) {
                                    newNewRecords.push(r);
                                }
                            });
                        } else {
                            ids.push(selectedRow.id);
                        }
                    }, this);
                    this.store.newRecords = newNewRecords;
                    if (!ids.length) return;

                    params[this.store.reader.meta.id] = ids.join(';');

                    this.el.mask(trlKwf('Deleting...'));
                    Ext.Ajax.request({
                        url: this.controllerUrl+'/json-delete',
                        params: params,
                        success: function(response, options, r) {
                            this.activeId = null;
                            //wenn gelöscht alle anderen disablen
                            this.bindings.each(function(i) {
                                i.item.disable();
                                i.item.reset();
                            }, this);

                            this.reload();
                            this.fireEvent('deleterow', this.grid);
                            this.fireEvent('datachange', r);

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
    onDuplicate : function() {
        var selectedRows = this.getGrid().getSelectionModel().getSelections();

        var ids = [];
        var params = this.getBaseParams() || {};
        selectedRows.each(function(selectedRow) {
            if (selectedRow.data.id != 0) {
                ids.push(selectedRow.id);
            }
        }, this);
        if (!ids.length) {
            Ext.Msg.show({
                title: trlKwf('Duplicate'),
                msg: trlKwf('No entries are selected')
            });
            return;
        }
        params[this.store.reader.meta.id] = ids.join(';');

        this.el.mask(trlKwf('Duplicating...'));
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-duplicate',
            params: params,
            success: function(response, options, r) {
                this.reload({
                    duplicatedIds: r.data.duplicatedIds,
                    callback: function(records, options, success) {
                        //neue einträge auswählen
                        if (options.duplicatedIds) {
                            var records = [];
                            options.duplicatedIds.each(function(id) {
                                records.push(this.getStore().getById(id));
                            }, this);
                            this.getSelectionModel().selectRecords(records);
                        }
                    },
                    scope: this
                });
                this.fireEvent('datachange', r);
            },
            callback: function() {
                this.el.unmask();
            },
            scope : this
        });
    },
    onPdf : function()
    {
        window.open(this.controllerUrl+'/pdf?'+Ext.urlEncode(this.getStore().baseParams));
    },
    onCsv : function()
    {
        Ext.Ajax.request({
            url : this.controllerUrl+'/json-csv',
            params  : this.getStore().baseParams,
            timeout: 600000, // 10 minuten
            progress: true,
            progressTitle : trlKwf('CSV export'),
            success: function(response, opt, r) {
                if (Ext.isIE) {
                    Ext.Msg.show({
                        title: trlKwf('Your download is ready'),
                        msg: trlKwf('Please click on the following link to download your CSV file.')
                            +'<br /><br />'
                            +'<a class="xlsExportLink" href="'+this.controllerUrl+'/download-csv-export-file?downloadkey='+r.downloadkey+'" target="_blank">'
                            +trlKwf('CSV export file')+'</a>',
                        icon: Ext.Msg.INFO,
                        buttons: { ok: trlKwf('Close') }
                    });
                } else {
                    Ext.getBody().createChild({
                        html: '<iframe width="0" height="0" src="'+this.controllerUrl+'/download-csv-export-file?downloadkey='+r.downloadkey+'"></iframe>'
                    });
                }
            },
            scope: this
        });
    },
    onXls : function()
    {
        var params = Kwf.clone(this.getStore().baseParams);
        if(this.getStore().sortInfo){
            var pn = this.getStore().paramNames;
            params[pn["sort"]] = this.getStore().sortInfo.field;
            params[pn["dir"]] = this.getStore().sortInfo.direction;
        }

        Ext.Ajax.request({
            url : this.controllerUrl+'/json-xls',
            params  : params,
            timeout: 600000, // 10 minuten
            progress: true,
            progressTitle : trlKwf('Excel export'),
            success: function(response, opt, r) {
                var downloadUrl = this.controllerUrl+'/download-export-file?downloadkey='+r.downloadkey;
                for (var i in params) {
                    downloadUrl += '&' + i + '=' + params[i];
                }
                if (Ext.isIE) {
                    Ext.Msg.show({
                        title: trlKwf('Your download is ready'),
                        msg: trlKwf('Please click on the following link to download your Excel file.')
                            +'<br /><br />'
                            +'<a class="xlsExportLink" href="'+downloadUrl+'" target="_blank">'
                            +trlKwf('Excel export file')+'</a>',
                        icon: Ext.Msg.INFO,
                        buttons: { ok: trlKwf('Close') }
                    });
                } else {
                    Ext.getBody().createChild({
                        html: '<iframe width="0" height="0" src="'+downloadUrl+'"></iframe>'
                    });
                }
            },
            scope: this
        });
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
        if (this.getGrid() && this.getGrid().getSelectionModel().grid) {
            this.getGrid().getSelectionModel().clearSelections();
        }
    },
    selectRow: function(row) {
        this.getSelectionModel().selectRow(row);
    },

    //für AbstractPanel
    selectId: function(id) {
        if (!this.getStore()) {
            //TODO: wenn benötigt id merken und beim ersten laden row auswählen
            return;
        }
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
                mask: this.el,
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

    getGrid : function() {
        return this.grid;
    },
    getView : function() {
        return this.getGrid().getView();
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
        if (this.getStore()) {
            return this.getStore().baseParams;
        } else {
            return this.baseParams || {};
        }
    },
    setBaseParams : function(baseParams) {
        baseParams = Kwf.clone(baseParams);
        if (this.editDialog && this.editDialog.setBaseParams) {
            this.editDialog.setBaseParams(baseParams);
        }
        if (this.getStore()) {
            this.getStore().baseParams = baseParams;
        } else {
            //no store yet, apply them later
            this.baseParams = baseParams;
        }
    },
    applyBaseParams : function(baseParams) {
        if (this.editDialog && this.editDialog.applyBaseParams) {
            this.editDialog.applyBaseParams(baseParams);
        }
        if (this.getStore()) {
            Ext.apply(this.getStore().baseParams, baseParams);
        } else {
            //no store yet, apply them later
            if (!this.baseParams) this.baseParams = {};
            Ext.apply(this.baseParams, baseParams);
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
    }
});

Ext.reg('kwf.autogrid', Kwf.Auto.GridPanel);
