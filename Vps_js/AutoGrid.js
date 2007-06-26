Vps.AutoGrid = function(renderTo, config)
{
    Ext.apply(this, config);
    this.events = {
        'generatetoolbar': true,
        'rowselect': true,
        'rowdblclick': true,
        'beforerowselect': true
    };

    this.renderTo = renderTo;

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

    this.ds.on({'metachange' : {
        fn: this.onMetaChange,
        scope: this,
        delay: 1  //damit das catch vom JsonReader nicht fehler schluckt
    }});

    if(this.autoload) {
        this.ds.load({params: {start: 0}});
    }
};

Ext.extend(Vps.AutoGrid, Ext.util.Observable,
{
    autoload: true,
    onMetaChange : function(store, meta) {
        var config = [];
        for (var i=0; i<meta.gridColumns.length; i++) {
            var column = meta.gridColumns[i];
            if (!column.header) continue;
            if (column.editor == 'Checkbox') {
                column.editor = new Ext.grid.GridEditor(new Ext.form.Checkbox());
            } else if (column.editor == 'TextField') {
                column.editor = new Ext.grid.GridEditor(new Ext.form.TextField())
            } else if (column.editor == 'DateField') {
                column.editor = new Ext.grid.GridEditor(new Ext.form.DateField({
                                                        format: "d.m.Y"
                                                    }))
            } else if (column.editor == 'NumberField') {
                column.editor = new Ext.grid.GridEditor(new Ext.form.NumberField({
                                    decimalSeparator: ",",
                                    msgTarget: "qtip"
                                    }))
            } else if (column.editor != '') {
                try {
                    column.editor = eval(column.editor);
                } catch(e) {
                    throw "invalid editor: "+column.editor;
                }
            }
            try {
                column.renderer = eval(column.renderer);
            } catch(e) {
                throw "invalid renderer: "+column.renderer;
                delete column.renderer;
            }
            if (column.defaultValue) delete column.defaultValue;
            if (column.dateFormat) delete column.dateFormat;
            config.push(column);
        }
        var colModel = new Ext.grid.ColumnModel(config);
        colModel.defaultSortable = true;

        var gridConfig = {
            dataSource: store,
            selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
            colModel: colModel
        };
        if (meta.gridUseEditor) {
            this.grid = new Ext.grid.EditorGrid(this.renderTo, gridConfig);
        } else {
            this.grid = new Ext.grid.Grid(this.renderTo, gridConfig);
        }

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
        this.grid.render();

        if (meta.gridPaging) {
            var paging = new Ext.PagingToolbar(this.grid.getView().getFooterPanel(true),
                store, {
                    pageSize: meta.gridPaging,
                    displayInfo: true
                });
        }

        this.toolbar = new Ext.Toolbar(this.grid.getView().getHeaderPanel(true));
        if (meta.gridButtons.save && meta.gridUseEditor) {
            this.saveButton = this.toolbar.addButton({
                text    : 'Speichern',
                disabled: true,
                handler : this.onSave,
                scope: this
            });
            this.toolbar.addSeparator();
        }

        if (meta.gridButtons.add && meta.gridUseEditor) {
            this.newButton = this.toolbar.addButton({
                text    : 'Neu',
                handler : this.onAdd,
                scope: this
            });
        }

        if (meta.gridButtons.delete) {
            this.deleteButton = this.toolbar.addButton({
                text    : 'Löschen',
                disabled: true,
                handler : this.onDelete,
                scope: this
            });
        }
        if (meta.gridFilters.text) {
            this.toolbar.addText("Filter:");
            this.toolbar.el.swallowEvent(['keypress','keydown']);
            var textfield = new Ext.form.TextField();
            this.toolbar.addField(textfield);
            textfield.getEl().on('keypress', function() {
                store.baseParams.filterText = textfield.getValue();
                if (store.reader.meta.gridPaging) {
                    store.load({params:{start:0}});
                } else {
                    store.load();
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

        //this should be in Ext.data.Record: fixme: send patch to ext.js
        var data = {};
        for(var i=0; i<this.ds.recordType.prototype.fields.items.length; i++) {
            data[this.ds.recordType.prototype.fields.items[i].name] = this.ds.recordType.prototype.fields.items[i].defaultValue;
        }
        var record = new this.ds.recordType({});

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
    }
});