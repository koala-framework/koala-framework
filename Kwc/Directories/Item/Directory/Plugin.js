Ext2.namespace('Kwc.Directories.Plugin');
Kwc.Directories.Plugin.GridWindow = function(config) {
    Ext2.apply(this, config);
};
Ext2.extend(Kwc.Directories.Plugin.GridWindow, Ext2.util.Observable,
{
    init: function(gridPanel)
    {
        if (gridPanel instanceof Kwc.Directories.Item.Directory.TabsPanel) {
            gridPanel = gridPanel.grid;
        }
        if (gridPanel instanceof Kwf.Binding.ProxyPanel) {
            gridPanel = gridPanel.proxyItem;
        }
        gridPanel.on('beforerendergrid', function(grid) {
            grid.getTopToolbar().add({
                text    : this.text,
                handler : function(o, p) {
                    var dlg = new Ext2.Window({
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
