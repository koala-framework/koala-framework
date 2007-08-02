Ext.namespace('Vpc.Paragraphs');
Vpc.Paragraphs.Index = function(renderTo, config)
{
    Ext.apply(this, config);
    this.events = {'editcomponent' : true};
    this.grid = new Vps.Auto.Grid(renderTo, config);
    this.grid.on('generatetoolbar', this.addButtons, this);
};

Ext.extend(Vpc.Paragraphs.Index, Ext.util.Observable,
{
    addButtons : function(toolbar)
    {
        var componentMenu = new Ext.menu.Menu({id: 'componentMenu'});
        for (var i in this.components) {
            componentMenu.addItem(
                new Ext.menu.Item({
                    id: i,
                    text: this.components[i],
                    handler: this.add,
                    baseParams: {id: this.id},
                    scope: this
                })
            );
        }
        this.addButton = toolbar.addButton({
            text    : 'Absatz hinzufügen',
            menu: componentMenu,
			handler: this.add
        });
        toolbar.addSeparator();
        this.editButton = toolbar.addButton({
            text    : 'Absatz Bearbeiten',
            handler : this.edit,
            scope   : this
        });
    },

    edit : function(o, p) {
        var row = this.grid.grid.getSelectionModel().getSelected();
        this.fireEvent('editcomponent', {id: row.data.id, pid: row.data.page_id, cls: row.data.component_class,  text:row.data.component_class})
    },

	add : function(o, e) {
        this.fireEvent('addcomponent', o.id, this.grid);
	}

})
