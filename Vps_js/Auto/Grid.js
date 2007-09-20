Vps.Auto.GridPanel = Ext.extend(Ext.grid.EditorGridPanel,
{
    controllerUrl: '',

    autoload: true,

    clicksToEdit: 1,

    initComponent : function(){
//todo:        id: this.controllerUrl.replace(/\//g, '-').replace(/^-|-$/g, '') //um eine eindeutige id für den stateManager zu haben

        //toolbars oben und unten immer erstellen
        this.elements += ',tbar';
        this.elements += ',bbar';

        if (!this.store) {
            this.store = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: this.controllerUrl + 'jsonData'}),
                reader: new Ext.data.JsonReader(),
                remoteSort: true
            });
        }
        this.store.newRecords = []; //hier werden neue records gespeichert die nicht dirty sind
        this.store.on('update', function(store, record, operation) {
            if (operation == Ext.data.Record.EDIT) {
                if (this.getTopToolbar().items.item('save')) {
                    this.getTopToolbar().items.item('save').enable();
                }
            }
        }, this);
        this.store.on('add', function(store, records, index) {
            if (this.getTopToolbar().items.item('save')) {
                this.getTopToolbar().items.item('save').enable();
            }
        }, this);

        this.store.on('metachange', this.onMetaChange, this);
        this.store.on('loadexception', function(proxy, o, response, e) {
            throw e; //re-throw
        }, this);

        this.store.on('load', function(store, records, options) {
            this.fireEvent('load', store, records, options);
        }, this);

        if (!this.selModel) {
            this.selModel = new Ext.grid.RowSelectionModel({singleSelect:true});
        }
        if (!this.colModel) {
            this.colModel = new Ext.grid.ColumnModel([{header: "", hidden:true}]); //workaround weil es ein columnmodel geben muss
        }

        this.selModel.on('rowselect', function(selData, gridRow, currentRow) {
            if (this.getTopToolbar().items.item('delete')) {
                this.getTopToolbar().items.item('delete').enable();
            }
        }, this);

        this.addEvents({
            'generatetoolbar': true,
//             'rowselect': true,
//             'beforerowselect': true,
            'load': true
        });
//todo:     this.grid.restoreState();
        if(this.autoload) {
            this.store.load();
        }

        Vps.Auto.GridPanel.superclass.initComponent.call(this);
    },

    onMetaChange : function(store, meta) {
        var config = [];
        for (var i=0; i<meta.columns.length; i++) {
            var column = meta.columns[i];
            if (!column.header) continue;

            if (column.editor && column.editor.type == 'Checkbox') {
                delete column.editor;
                if (column.renderer) delete column.renderer;
                column = new Vps.Grid.CheckColumn(column);
                column.init(this);
            } else if (column.editor) {
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
                    this.on('validateedit', function(e) {
                        if(e.field == this.column.dataIndex){
                            e.record.data[this.column.showDataIndex] = this.field.getRawValue();
                        }
                    }, {field: field, column: column});
                }
            }

            if (typeof column.renderer == 'function') {
                //do nothing
            } else if (Vps.Renderer[column.renderer]) {
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
            column.sortable = meta.sortable;
            config.push(column);
        }
        this.colModel = new Ext.grid.ColumnModel(config);

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

        this.view.initData(this.store, this.colModel);
        this.view.refresh(true);
//reconfigure macht im prinzip das gleiche wie das oben, funktionert aber irgendwie nicht richtig
//         this.reconfigure(this.store, this.colModel);

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
                pagingConfig.renderTo = this.bbar;
                this.bottomToolbar = new t(pagingConfig);
                this.syncSize(); //workaround, should be fixed in ext (funkt owa eh ned)
            } else {
                this.pagingType = 'Ext.PagingToolbar';
                this.bottomToolbar = new Ext.PagingToolbar({
                        store: this.store,
                        pageSize: meta.paging,
                        displayInfo: true,
                        renderTo: this.bbar
                    });
                this.syncSize(); //workaround, should be fixed in ext (funkt owa eh ned)
            }
        } else {
            this.pagingType = false;
        }

        //toolaber erstellen falls es noch keine gibt und buttons definiert sind
        if (!this.topToolbar && meta.buttons != {}) {
            this.topToolbar = new Ext.Toolbar({renderTo: this.tbar});
        }

        if (meta.buttons.reload) {
            this.getTopToolbar().add({
                id      : 'reload',
                text    : '',
                handler : function () { this.reload(); },
                icon : '/assets/vps/images/silkicons/bullet_star.png',
                cls: 'x-btn-icon',
                scope   : this
            });
        }
        
        if (meta.buttons.save) {
            this.getTopToolbar().add({
                id      : 'save',
                text    : 'Save',
                icon    : '/assets/vps/images/silkicons/table_save.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                handler : this.onSave,
                scope: this
            });
            this.getTopToolbar().addSeparator();
        }

        if (meta.buttons.add) {
            this.getTopToolbar().add({
                id      : 'add',
                text    : 'Add',
                icon    : '/assets/vps/images/silkicons/table_add.png',
                cls     : 'x-btn-text-icon',
                handler : this.onAdd,
                scope: this
            });
        }

        if (meta.buttons['delete']) {
            this.getTopToolbar().add({
                id      : 'delete',
                text    : 'Delete',
                icon    : '/assets/vps/images/silkicons/table_delete.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                handler : this.onDelete,
                scope: this
            });
        }
        if (meta.buttons.pdf) {
            if(this.getTopToolbar().items.length > 0) {
                this.getTopToolbar().addSeparator();
            }
            this.getTopToolbar().add({
                text    : 'Drucken',
                icon    : '/assets/vps/images/silkicons/printer.png',
                cls     : 'x-btn-text-icon',
                handler : this.onPdf,
                scope: this
            });
        }

        if (meta.filters.text) {
            if(this.getTopToolbar().items.length > 0) {
                this.getTopToolbar().addSeparator();
            }
            this.getTopToolbar().addText("Filter:");
            this.getTopToolbar().el.swallowEvent(['keypress','keydown']);
            var textfield = new Ext.form.TextField();
            this.getTopToolbar().addField(textfield);
            textfield.getEl().on('keypress', function() {
                this.store.baseParams.query = textfield.getValue();
                if (this.pagingType && this.pagingType != 'Date') {
                    this.store.load({params:{start:0}});
                } else {
                    this.store.load();
                }
            }, this, {buffer: 500});
            delete meta.filters.text;
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
                this.getTopToolbar().addText(" ");
                this.getTopToolbar().addField(combo);
                combo.on('select', function(combo, record, index) {
                    this.store.baseParams['query_'+filter] = record.id;
                    this.load({start:0});
                }, this);
            }
        }
        this.fireEvent('generatetoolbar');
    },
    onSave : function()
    {
        this.getTopToolbar().items.item('save').disable();
        var data = [];
        var modified = this.store.getModifiedRecords();
        if (!modified.length) return;
        //geänderte records
        modified.each(function(r) {
            this.store.newRecords.remove(r); //nur einmal speichern
            data.push(r.data);
        }, this);

        //neue, ungeänderte records
        this.store.newRecords.each(function(r) {
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
                this.getTopToolbar().items.item('save').enable();
            },
            callback: function() {
                Ext.get(document.body).unmask();
            },
            scope  : this
        });
    },
    onAdd : function() {

        var data = {};
        for(var i=0; i<this.store.recordType.prototype.fields.items.length; i++) {
            data[this.store.recordType.prototype.fields.items[i].name] = this.store.recordType.prototype.fields.items[i].defaultValue;
        }
        var record = new this.store.recordType(data);

        this.stopEditing();
        this.store.insert(0, record);
        this.store.newRecords.push(record);
        
        for(var i=0; i<this.getColumnModel().getColumnCount(); i++) {
            if(!this.getColumnModel().isHidden(i) && this.getColumnModel().isCellEditable(i, 0)) {
                this.startEditing(0, i);
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
                    var selectedRow = this.getSelectionModel().getSelected();
                    if (!selectedRow) return;
                    if (selectedRow.data.id == 0) {
                        this.store.remove(selectedRow);
                    } else {
                        Ext.get(document.body).mask('löschen...', 'x-mask-loading');
                        var params = {};
                        params[this.store.reader.meta.id] = selectedRow.id;
                        Ext.Ajax.request({
                            url: this.controllerUrl+'jsonDelete',
                            params: params,
                            success: function() {
                                this.reload();
                                this.getTopToolbar().items.item('delete').disable();
                            },
                            failure: function() {
                                this.getTopToolbar().items.item('delete').enable();
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
    onPdf : function()
    {
        window.open(this.controllerUrl+'pdf');
    },
    getSelected: function() {
        return this.getSelectionModel().getSelected();
    },
    clearSelections: function() {
        this.getSelectionModel().clearSelections();
    },
    selectRow: function(row) {
        this.getSelectionModel().selectRow(row);
    },
    reload: function() {
        this.store.reload();
        this.store.commitChanges();
    },
    load : function(params) {
        if(!params) params = {};
        this.loadParams = params; //submit them again on save
        this.store.load({params:params});
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
        this.store.removeAll();
    }
});
