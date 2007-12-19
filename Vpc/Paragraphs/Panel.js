Ext.namespace('Vpc.Paragraphs');
Vpc.Paragraphs.Panel = Ext.extend(Vps.Auto.GridPanel,
{
    initComponent : function()
    {
        this.addEvents('editcomponent');
        Vpc.Paragraphs.Panel.superclass.initComponent.call(this);
        this.on('rendergrid', this.addButtons, this);
    },

    addButtons : function()
    {
        var toolbar = this.grid.getTopToolbar();
        toolbar.addSeparator();
        var componentMenu = new Ext.menu.Menu({id: 'componentMenu'});
        this.addComponents(this.components, componentMenu);
        this.addButton = toolbar.addButton({
            text    : 'Add Paragraph',
            menu: componentMenu
        });
        toolbar.addSeparator();
        this.editButton = toolbar.addButton({
            text    : 'Edit Paragraph',
            handler : this.onEdit,
            scope   : this
        });
        this.getGrid().on('cellclick', function(grid, rowIndex, columnIndex) {
            if (columnIndex == 3) {
                var row = grid.getStore().getAt(rowIndex);
                var bp = this.getBaseParams();
                this.fireEvent('editcomponent', {
                    componentClass: row.data.component_class,
                    pageId: bp.page_id,
                    componentKey: bp.component_key + '-' + row.data.id,
                    text: row.data.component_name
                });
            }
        }, this);
    },

    addComponents : function(components, addToItem)
    {
        if (components.length == 0) { return; }
        for (var i in components) {
            if (typeof components[i] == 'string') {
                addToItem.addItem(
                    new Ext.menu.Item({
                        id: components[i],
                        text: i,
                        handler: this.onParagraphAdd,
                        scope: this
                    })
                );
            } else {
                var item = new Ext.menu.Item({text: i, menu: []});
                addToItem.addItem(item);
                this.addComponents(components[i], addToItem.items.items[addToItem.items.length - 1].menu);
            }
        }
    },

    onEdit : function(o, p) {
        var row = this.grid.getSelectionModel().getSelected();
        if (row != undefined) {
            var bp = this.getBaseParams();
            this.fireEvent('editcomponent', {
                componentClass: row.data.component_class,
                pageId: bp.page_id,
                componentKey: bp.component_key + '-' + row.data.id,
                text: row.data.component_name
            });
        }
    },

    onParagraphAdd : function(o, e) {
        Ext.Ajax.request({
            url: this.controllerUrl + '/jsonAddParagraph',
            params: Ext.apply ({ component : o.id}, this.getBaseParams()),
            success: function(r) {
                this.reload();
            },
            scope: this
        });
    }
});
