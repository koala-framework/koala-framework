Ext.namespace('Vpc.Formular');
Vpc.Formular.Index = function(renderTo, config)
{
    this.layout = new Ext.BorderLayout(
        renderTo,
        {
            center: {
                initialSize: 700,
                titlebar: true,
                collapsible: true,
                minSize: 200,
                maxSize: 600
            },
            east: {
                split:true,
                initialSize: 500,
                titlebar: true,
                collapsible: true,
                minSize: 200,
                maxSize: 600
            }
        }
    );
    this.grid = new Vps.Auto.Grid(renderTo.createChild(), config);
    this.layout.add("center", new Ext.ContentPanel(this.grid, {autoCreate: true, title: 'Formular Elemente'}));
    this.layout.add("east", new Ext.ContentPanel("generalProperties", {autoCreate: true, title: 'Einstellungen'}));
    this.grid.on('generatetoolbar', this.addButtons, this);
};


Ext.extend(Vpc.Formular.Index, Ext.util.Observable,
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
    
    edit : function(o) {
        var controllerUrl = '/component/edit/' + o.cls + '/' + o.pid + '-' + o.id + '/';
        Ext.Ajax.request({
            url: controllerUrl + 'jsonIndex/',
            success: function(r) {
                response = Ext.decode(r.responseText);
                cls = eval(response['class']);
        this.layout.remove("east", "generalProperties");
        this.layout.add("east", new Ext.ContentPanel("generalProperties", {autoCreate: true, title: 'Einstellungen'}));
        component = new cls('generalProperties', Ext.applyIf(response.config, {controllerUrl: controllerUrl, fitToFrame:true}));
            },
            scope: this
        });
    },
    
    add : function(id, grid) {
      var Row = Ext.data.Record.create([
           {name: 'component_class', type: 'string'},
           {name: 'visible', type: 'bool'},
           {name: 'name', type: 'string'},
           {name: 'mandatory', type: 'bool'},
           {name: 'no_cols', type: 'bool'},
           {name: 'page_id', type: 'int'},
       {name: 'id', type: 'int'}
      ]);

    if (typeof id == 'undefined'){
      alert ('undefined');
    }
    else {
      var entry = new Row ({
        component_class: id,
        visible: true,
        name: 'undefined',
        mandatory: false,
        no_cols: false,
        page_id: 0,
        id: 0
      });

      grid.grid.stopEditing();
            grid.ds.insert(0, entry);
            grid.grid.startEditing(0, 0);
    }
  }
})




