Ext.namespace('Vpc.Paragraphs');
Vpc.Paragraphs.Index = function(renderTo, config)
{
    Ext.apply(this, config);
    this.events = {};
    this.grid = new Vps.Auto.Grid(renderTo, config);
    this.grid.on('generatetoolbar', this.addButtons, this);
};

Ext.extend(Vpc.Paragraphs.Index, Ext.util.Observable,
{
    getPanel : function(title)
    {
        return new Ext.GridPanel(this.grid.grid, {autoCreate:true, title: title, fitToFrame:true, closable:true, autoScroll: true, fitContainer: true});
    },
    
    addButtons : function(toolbar)
    {
        var componentMenu = new Ext.menu.Menu({id: 'componentMenu'});
        this.addComponents(this.components, componentMenu);
        this.addButton = toolbar.addButton({
            text    : 'Absatz hinzufügen',
            menu: componentMenu
        });
        toolbar.addSeparator();
        this.editButton = toolbar.addButton({
            text    : 'Absatz Bearbeiten',
            handler : this.edit,
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
                        handler: this.add,
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

    edit : function(o, p) {
        var row = this.grid.grid.getSelectionModel().getSelected();
        controllerUrl = row.id.replace(/\/show\//, '/edit/');
        this.fireEvent('editcomponent', {controllerUrl: controllerUrl, text: this.text + ': ' + row.data.pos});
    },

    add : function(o, e) {
        Ext.Ajax.request({
            url: this.controllerUrl + 'jsonAddParagraph/',
            params: {component : o.id},
            success: function(r) {
                response = Ext.decode(r.responseText);
                //debugger;
            },
            scope: this
        });
    }

})
