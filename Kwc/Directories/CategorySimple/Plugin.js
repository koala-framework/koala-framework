Ext2.namespace('Kwc.Directories.CategorySimple');
Kwc.Directories.CategorySimple.Plugin = function(config) {
    config.text = trlKwf('Categories');
    Ext2.apply(this, config);
};

Ext2.extend(Kwc.Directories.CategorySimple.Plugin, Ext2.util.Observable,
{
    init: function(gridPanel)
    {
        if (gridPanel instanceof Kwc.Directories.Item.Directory.TabsPanel) {
            gridPanel = gridPanel.grid;
        }
        gridPanel.grid.on('beforerendergrid', function(grid) {
            grid.getTopToolbar().add({
                text    : this.text,
                handler : function(o, p) {
                    var dlg = new Ext2.Window({
                        width:  this.width || 600,
                        height: this.height || 600,
                        layout: 'fit',
                        title:  this.text,
                        modal:  true,
                        items:  new Kwf.Auto.TreePanel({
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

