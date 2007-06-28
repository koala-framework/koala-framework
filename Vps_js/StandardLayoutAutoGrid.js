Vps.StandardLayoutAutoGrid = function(renderTo, config)
{
    Ext.apply(this, config);

    var layout = new Vps.StandardLayout(renderTo);
    var grid = new Vps.AutoGrid('workspaceContainer', config);
};

Ext.extend(Vps.StandardLayoutAutoGrid, Ext.util.Observable,
{

});
