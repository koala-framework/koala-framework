Ext.namespace('Vpc.Directories.Category.Directory');
Vpc.Directories.Category.Directory.Plugin = function(config) {
    Ext.apply(this, config);
};
Ext.extend(Vpc.Directories.Category.Directory.Plugin, Ext.util.Observable,
{
    init: function(newsPanel)
    {
        newsPanel.on('beforerendergrid', function(grid) {
            grid.getTopToolbar().add({
                text    : trlVps('Categories'),
                handler : function(o, p) {
                    var dlg = new Ext.Window({
                        width:  450,
                        height: 370,
                        layout: 'fit',
                        title:  trlVps('Categories'),
                        modal:  true,
                        items:  new Vps.Auto.GridPanel({
                            controllerUrl: this.controllerUrl,
                            baseParams: newsPanel.getBaseParams()
                        })
                    }, this);
                    dlg.show();
                },
                scope   : this
            });
        }, this);
    }
});
