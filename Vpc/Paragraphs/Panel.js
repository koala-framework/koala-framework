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
    },

    addComponents : function(components, addToItem)
    {
        for (var i in components) {
            if (typeof components[i] == 'string') {
                addToItem.addItem(
                    new Ext.menu.Item({
                        id: components[i],
                        text: i,
                        handler: this.onAdd,
                        baseParams: {id: this.id},
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
            controllerUrl = row.data.page_id.replace(/\/show\//, '/edit/');
            this.fireEvent('editcomponent', {controllerUrl: controllerUrl, text: row.data.component_class});
        }
    },

    onAdd : function(o, e) {
        Ext.Ajax.request({
            url: this.controllerUrl + '/jsonAddParagraph',
            params: {component : o.id},
            success: function(r) {
                this.reload();
            },
            scope: this
        });
    }
});
