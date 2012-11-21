Ext.namespace('Kwc.Directories.Plugin');
Kwc.Directories.Plugin.GridWindow = function(config) {
    Ext.apply(this, config);
};
Ext.extend(Kwc.Directories.Plugin.GridWindow, Ext.util.Observable,
{
    init: function(gridPanel)
    {
        if (gridPanel instanceof Kwc.Directories.Item.Directory.TabsPanel) {
            gridPanel = gridPanel.grid;
        }
        gridPanel.on('beforerendergrid', function(grid) {
            grid.getTopToolbar().add({
                text    : this.text,
                handler : function(o, p) {
                    var dlg = new Ext.Window({
                        width:  this.width || 450,
                        height: this.height || 370,
                        layout: 'fit',
                        title:  this.text,
                        modal:  true,
                        items:  new Kwf.Auto.GridPanel({
                            controllerUrl: this.controllerUrl,
                            baseParams: gridPanel.getBaseParams()
                        })
                    }, this);
                    dlg.show();
                },
                scope   : this
            });
        }, this);
    }
});
