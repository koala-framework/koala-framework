Ext.namespace('Vpc.Paragraphs');
Vpc.Paragraphs.Index = function(renderTo, config)
{
    Ext.apply(this, config);
    this.events = {
        'editcomponent' : true
    };
    this.config = config;
    this.config.controllerUrl = this.config.controllerUrl || '';
    var Paragraph = Ext.data.Record.create([
        {name: 'id', type: 'int'},
        {name: 'pos', type: 'int'},
        {name: 'component', type: 'string'},
        {name: 'visible', type: 'boolean'}
    ]);
    
    this.ds = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy(new Vps.Connection({url: this.config.controllerUrl + 'ajaxData'})),
        reader: new Ext.data.JsonReader({root: 'rows', id: 'id'}, Paragraph)
    });
    this.ds.load();

    var colModel = new Ext.grid.ColumnModel([
            {   header: "Type", 
                width: 300, 
                dataIndex: 'component'
            },{
                header: "Sichtbar", 
                width: 100, 
                dataIndex: 'visible'
            }
    ]);
    
    this.grid = new Ext.grid.EditorGrid(renderTo, {
        ds: this.ds,
        selModel: new Ext.grid.RowSelectionModel(),
        cm: colModel
    });

    this.grid.render();

    this.conn = new Vps.Connection();

    // Toolbar
    var toolbar = new Ext.Toolbar(this.grid.getView().getHeaderPanel(true));

    var componentMenu = new Ext.menu.Menu({id: 'componentMenu'});
    for (var i in config.components) {
        componentMenu.addItem(
            new Ext.menu.Item({
                id: i,
                text: config.components[i],
                handler: this.onAdd,
                baseParams: {id: config.id},
                scope: this
            })
        );
    }
    this.addButton = toolbar.addButton({
        text    : 'Absatz hinzufügen',
        menu: componentMenu
    });
    toolbar.addSeparator();
    this.deleteButton = toolbar.addButton({
        text    : 'Absatz löschen',
        handler : this.onDelete,
        baseParams: {id: config.id},
        scope   : this
    });
    toolbar.addSeparator();
    this.visibleButton = toolbar.addButton({
        text    : 'Sichtbar',
        handler : this.onVisible,
        baseParams: {id: config.id, visible:'visible'},
        scope   : this
    });
    this.invisibleButton = toolbar.addButton({
        text    : 'Unsichtbar',
        handler : this.onVisible,
        baseParams: {id: config.id, visible:'invisible'},
        scope   : this
    });
    toolbar.addSeparator();
    this.downButton = toolbar.addButton({
        text    : '˅',
        handler : this.move,
        baseParams: {id: config.id, direction:'down'},
        scope   : this
    });
    this.upButton = toolbar.addButton({
        text    : '˄',
        handler : this.move,
        baseParams: {id: config.id, direction:'up'},
        scope   : this
    });
    toolbar.addSeparator();
    this.editButton = toolbar.addButton({
        text    : 'Absatz Bearbeiten',
        handler : this.edit,
        scope   : this
    });

    this.getSelectedIds = function()
    {
        var selectedRows = this.grid.getSelectionModel().getSelections();
        if (selectedRows.length > 0) {
            var componentIds = new Array();
            for (var x = 0; x < selectedRows.length; x++) {
                componentIds[x] = selectedRows[x].data.id;
            }
            return componentIds.join(',');
        } else {
            Ext.Msg.alert('Error', 'No rows selected.');
            return '';
        }
    }
    
};

Ext.extend(Vpc.Paragraphs.Index, Ext.util.Observable,
{
    onAdd : function(o, p) {
        var selectedRow = this.grid.getSelectionModel().getSelected();
        if (selectedRow) {
            componentId = selectedRow.data.id;
        } else {
            componentId = 0;
        }
        new Vps.Connection().request({
            url: this.config.controllerUrl + 'ajaxCreate',
            method: 'post',
            scope: this,
            params: {
                componentClass : o.id,
                componentId : componentId,
                id : o.baseParams.id
            },
            callback: function(options, bSuccess, response) {
                this.ds.reload();
            }
        });
    },

    onDelete : function(o, p) {
        componentIds = this.getSelectedIds();
        if (componentIds != '') {
            Ext.Msg.show({
                title:'Delete Paragraph',
                msg: 'Do you really want to delete the selected paragraphs?',
                buttons: Ext.Msg.YESNO,
                scope: this,
                fn: function(button) {
                    if (button == 'yes'){
                        new Vps.Connection().request({
                            url: this.config.controllerUrl + 'ajaxDelete',
                            method: 'post',
                            scope: this,
                            params: {
                                componentIds : componentIds,
                                id : o.baseParams.id
                            },
                            callback: function(options, bSuccess, response) {
                                this.ds.reload();
                            }
                        });
                    }
                }
            })
        }
    },

    onVisible : function(o, p) {
        componentIds = this.getSelectedIds();
        if (componentIds != '') {
            new Vps.Connection().request({
                url: this.config.controllerUrl + 'ajaxVisible',
                method: 'post',
                scope: this,
                params: {
                    componentIds : componentIds,
                    id : o.baseParams.id,
                    visible : o.baseParams.visible
                },
                callback: function(options, bSuccess, response) {
                    this.ds.reload();
                }
            });
        }
    },

    move : function(o, p) {
        componentIds = this.getSelectedIds();
        if (componentIds != '') {
            new Vps.Connection().request({
                url: this.config.controllerUrl + 'ajaxMove',
                method: 'post',
                scope: this,
                params: {
                    componentIds : componentIds,
                    id : o.baseParams.id,
                    direction : o.baseParams.direction
                },
                callback: function(options, bSuccess, response) {
                    this.ds.reload();
                }
            });
        }
    },

    edit : function(o, p) {
        componentIds = this.getSelectedIds();
        
        if (componentIds != '') {
            row = this.grid.getSelectionModel().getSelections().shift();
            this.fireEvent('editcomponent', {id: row.id, text:'foo'})
        }
    }

})
