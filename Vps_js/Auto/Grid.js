Vps.Auto.Grid = function(renderTo, config)
{
    Ext.apply(this, config);
    this.events = {
        'generatetoolbar': true,
        'rowselect': true,
        'rowdblclick': true,
        'beforerowselect': true,
        'load': true
    };

    this.renderTo = renderTo || Ext.get(document.body).createChild();

    this.ds = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({url: this.controllerUrl + 'jsonData'}),
        reader: new Ext.data.JsonReader(),
        remoteSort: true
    });
    this.ds.newRecords = []; //hier werden neue records gespeichert die nicht dirty sind
    this.ds.on('update', function(store, record, operation) {
        if (operation == Ext.data.Record.EDIT) {
            if(this.saveButton) this.saveButton.enable();
        }
    }, this);
    this.ds.on('add', function(store, records, index) {
        if(this.saveButton) this.saveButton.enable();
    }, this);

    this.ds.on('metachange', this.onMetaChange, this);
    this.ds.on('loadexception', function(proxy, o, response, e) {
        throw e; //re-throw
    }, this);

    this.ds.on('load', function(store, records, options) {
        this.fireEvent('load', store, records, options);
    }, this);

    this.grid = new Ext.grid.EditorGrid(this.renderTo, Ext.applyIf(config, {
        clicksToEdit: 1,
        dataSource: this.ds,
        selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
        colModel: new Ext.grid.ColumnModel([{header: "", hidden:true}]), //workaround weil es ain columnmodel geben muss
        id: this.controllerUrl.replace(/\//g, '-').replace(/^-|-$/g, '') //um eine eindeutige id für den stateManager zu haben
    }));

    this.grid.getSelectionModel().on('rowselect', function(selData, gridRow, currentRow) {
        if(this.deleteButton) this.deleteButton.enable();
        this.fireEvent('rowselect', selData, gridRow, currentRow);
    }, this);

    this.grid.getSelectionModel().on('beforerowselect', function(selData, gridRow, currentRow) {
        return this.fireEvent('beforerowselect', selData, gridRow, currentRow);
    }, this);

    this.grid.on('rowdblclick', function(selModel, rowIndex) {
        var data = this.grid.dataSource.data.items[rowIndex].data;
        this.fireEvent('rowdblclick', data, selModel, rowIndex);
    }, this);

    this.grid.restoreState();

    if(this.autoload) {
        this.ds.load();
    }
};

Ext.extend(Vps.Auto.Grid, Ext.util.Observable,
{
    autoload: true,
    onMetaChange : function(store, meta) {
        var config = [];
        for (var i=0; i<meta.columns.length; i++) {
            var column = meta.columns[i];
            if (!column.header) continue;


            if (column.editor) {
                var editorConfig = { msgTarget: 'qtip' };
                var type;
                if (typeof column.editor == 'string') {
                    type = column.editor;
                } else {
                    type = column.editor.type;
                    delete column.editor.type;
                    editorConfig = Ext.applyIf(column.editor, editorConfig);
                }
                if (Vps.Form[type]) {
                    column.editor = new Ext.grid.GridEditor(new Vps.Form[type](editorConfig));
                } else if (Ext.form[type]) {
                    column.editor = new Ext.grid.GridEditor(new Ext.form[type](editorConfig));
                } else if (type != '') {
                    try {
                        column.editor = eval(column.editor);
                    } catch(e) {
                        throw "invalid editor: "+column.editor;
                    }
                }
                var field = column.editor.field;
                if(field instanceof Ext.form.ComboBox) {
                    this.grid.on('validateedit', function(e) {
                        if(e.field == this.column.dataIndex){
                            e.record.data[this.column.showDataIndex] = this.field.getRawValue();
                        }
                    }, {field: field, column: column});
                }
            }

            if (Vps.Renderer[column.renderer]) {
                column.renderer = Vps.Renderer[column.renderer];
            } else if (Ext.util.Format[column.renderer]) {
                column.renderer = Ext.util.Format[column.renderer];
            } else if (column.renderer) {
                try {
                    column.renderer = eval(column.renderer);
                } catch(e) {
                    throw "invalid renderer: "+column.renderer;
                }
            } else if (column.showDataIndex) {
                column.renderer = Vps.Renderer.ShowField(column.showDataIndex);
            }

            if (column.defaultValue) delete column.defaultValue;
            if (column.dateFormat) delete column.dateFormat;
            config.push(column);
        }
        var colModel = new Ext.grid.ColumnModel(config);
        colModel.defaultSortable = meta.sortable;


        this.grid.colModel = colModel;
        this.grid.render();
        this.grid.restoreState();

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
                new t(this.grid.getView().getFooterPanel(true),
                    this.ds, meta.paging);
            } else {
                this.pagingType = 'Ext.PagingToolbar';
                new Ext.PagingToolbar(this.grid.getView().getFooterPanel(true),
                    this.ds, {
                        pageSize: meta.paging,
                        displayInfo: true
                    });
            }
        } else {
            this.pagingType = false;
        }
        if (meta.buttons.save) {
            this.saveButton = this.getToolbar().addButton({
                text    : 'Speichern',
                icon    : '/assets/vps/images/silkicons/table_save.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                handler : this.onSave,
                scope: this
            });
            this.getToolbar().addSeparator();
        }

        if (meta.buttons.add) {
            this.newButton = this.getToolbar().addButton({
                text    : 'Neu',
                icon    : '/assets/vps/images/silkicons/table_add.png',
                cls     : 'x-btn-text-icon',
                handler : this.onAdd,
                scope: this
            });
        }

        if (meta.buttons['delete']) {
            this.deleteButton = this.getToolbar().addButton({
                text    : 'Löschen',
                icon    : '/assets/vps/images/silkicons/table_delete.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                handler : this.onDelete,
                scope: this
            });
        }
        if (meta.filters.text) {
            if(this.getToolbar().items.length > 0) {
                this.getToolbar().addSeparator();
            }
            this.getToolbar().addText("Filter:");
            this.getToolbar().el.swallowEvent(['keypress','keydown']);
            var textfield = new Ext.form.TextField();
            this.getToolbar().addField(textfield);
            textfield.getEl().on('keypress', function() {
                this.ds.baseParams.query = textfield.getValue();
                if (this.pagingType && this.pagingType != 'Date') {
                    this.ds.load({params:{start:0}});
                } else {
                    this.ds.load();
                }
            }, this, {buffer: 500});
        }
        this.fireEvent('generatetoolbar', this.toolbar);
    },
    onSave : function()
    {
        this.saveButton.disable();
        var data = [];
        var modified = this.ds.getModifiedRecords();
        if (!modified.length) return;

        //geänderte records
        modified.each(function(r) {
            this.ds.newRecords.remove(r); //nur einmal speichern
            data.push(r.data);
        }, this);

        //neue, ungeänderte records
        this.ds.newRecords.each(function(r) {
            data.push(r.data);
        }, this);

        Ext.get(document.body).mask('speichern...', 'x-mask-loading');
        var params = this.loadParams || {};
        params.data = Ext.util.JSON.encode(data);
        Ext.Ajax.request({
            url: this.controllerUrl+'jsonSave',
            params: params,
            success: function() {
                this.reload();
            },
            failure: function() {
                this.saveButton.enable();
            },
            callback: function() {
                Ext.get(document.body).unmask();
            },
            scope  : this
        });
    },
    onAdd : function() {

        var data = {};
        for(var i=0; i<this.ds.recordType.prototype.fields.items.length; i++) {
            data[this.ds.recordType.prototype.fields.items[i].name] = this.ds.recordType.prototype.fields.items[i].defaultValue;
        }
        var record = new this.ds.recordType(data);

        this.grid.stopEditing();
        this.ds.insert(0, record);
        this.ds.newRecords.push(record);
        
        for(var i=0; i<this.grid.getColumnModel().getColumnCount(); i++) {
            if(!this.grid.getColumnModel().isHidden(i) && this.grid.getColumnModel().isCellEditable(i, 0)) {
                this.grid.startEditing(0, i);
                break;
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
                    var selectedRow = this.grid.getSelectionModel().getSelected();
                    if (!selectedRow) return;
                    if (selectedRow.data.id == 0) {
                        this.ds.remove(selectedRow);
                    } else {
                        Ext.get(document.body).mask('löschen...', 'x-mask-loading');
                        var params = {};
                        params[this.ds.reader.meta.id] = selectedRow.id;
                        Ext.Ajax.request({
                            url: this.controllerUrl+'jsonDelete',
                            params: params,
                            success: function() {
                                this.ds.remove(selectedRow);
                                this.deleteButton.disable();
                            },
                            failure: function() {
                                this.deleteButton.enable();
                            },
                            callback: function() {
                                Ext.get(document.body).unmask();
                            },
                            scope : this
                        });
                    }
                }
            }
        });
    },
    getSelected: function() {
        return this.grid.getSelectionModel().getSelected();
    },
    clearSelections: function() {
        this.grid.getSelectionModel().clearSelections();
    },
    selectRow: function(row) {
        this.grid.getSelectionModel().selectRow(row);
    },
    reload: function() {
        this.ds.reload();
        this.ds.commitChanges();
    },
    load : function(params) {
        if(!params) params = {};
        this.loadParams = params; //submit them again on save
        this.ds.load({params:params});
    },
    enable: function() {
        if(this.newButton) this.newButton.enable();
    },
    disable: function() {
        if (this.toolbar) {
            this.toolbar.items.each(function(b) {
                b.disable();
            }, this);
        }
        this.ds.removeAll();
    },
    getToolbar: function() {
        if (!this.toolbar) {
            this.toolbar = new Ext.Toolbar(this.grid.getView().getHeaderPanel(true));
        }
        return this.toolbar;
    }
});
