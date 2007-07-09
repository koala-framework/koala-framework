Vps.StandardLayoutAutoGrid = function(renderTo, config)
{
    Ext.apply(this, config);

    var layout = new Vps.StandardLayout(renderTo);
    var grid = new Vps.AutoGrid(Ext.get(document.body).createChild(), config);

    layout.beginUpdate();
    layout.add('center', new Ext.GridPanel(grid.grid, {autoCreate: true, fitToFrame:true, closable:false}));
    layout.endUpdate();
};

Ext.extend(Vps.StandardLayoutAutoGrid, Ext.util.Observable,
{

});
