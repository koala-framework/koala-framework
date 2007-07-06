Ext.namespace('Vps.Admin.Components');
Vps.Admin.Components.Index = function(renderTo, config)
{
    var layout = new Ext.BorderLayout(renderTo, {
        west: {
            split:true,
            initialSize: 455,
            titlebar: true
        },
        center: {
            autoScroll: true
        }
    });
    
    layout.add('west', new Ext.ContentPanel('gridContainer', {autoCreate: true, title:'Komponentenliste', fitToFrame:true, fitContainer:true}));
    layout.add('center', new Ext.ContentPanel('formContainer', {autoCreate:true, title:'Komponenteneigenschaften', fitToFrame:true}));
    
    var grid = new Vps.AutoGrid('gridContainer', config);
    
    grid.on('rowselect', function(selData, gridRow, currentRow) {
        //Ext.DomHelper.overwrite('formContainer', '');
        var form = new Vps.AutoForm.Form('formContainer', {controllerUrl:'/admin/componentsConfig/', baseParams: { id: currentRow.id}});
    });
    /*
    grid.onAdd = function() {
        console.log(grid);
    };
    */
}

Ext.extend(Vps.Admin.Components.Index, Ext.util.Observable,
{
    treeEditProperties : function (o, e) {
    }
}
)
