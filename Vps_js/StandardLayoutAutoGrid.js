Vps.StandardLayoutAutoGrid = function(renderTo, config)
{
    Ext.apply(this, config);
    this.render();
};

Ext.extend(Vps.StandardLayoutAutoGrid, Ext.util.Observable,
{
    render: function()
    {
        var grid = new Vps.Auto.Grid(Ext.get(document.body).createChild(), {controllerUrl: this.controllerUrl});

        this.mainLayoutPanel = new Ext.GridPanel(grid.grid, {closable:true, title: this.title});
        if (Vps.mainLayout) {
            var layout = Vps.mainLayout;
        } else {
            var layout = new Vps.StandardLayout(document.body);
        }
        layout.beginUpdate();
        layout.add('center', this.mainLayoutPanel);
        layout.endUpdate();
    },
    activate: function()
    {
        if(this.mainLayoutPanel.el) {
            Vps.mainLayout.showPanel(this.mainLayoutPanel);
        } else {
            this.render();
        }
    }
});
