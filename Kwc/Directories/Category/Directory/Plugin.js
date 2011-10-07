Ext.namespace('Kwc.Directories.Category.Directory');
Kwc.Directories.Category.Directory.Plugin = function(config) {
    Ext.apply(this, config);
};
Ext.extend(Kwc.Directories.Category.Directory.Plugin, Ext.util.Observable,
{
    init: function(newsPanel)
    {
        newsPanel.on('beforerendergrid', function(grid) {
            grid.getTopToolbar().add({
                text    : trlKwf('Categories'),
                handler : function(o, p) {
                    var dlg = new Ext.Window({
                        width:  450,
                        height: 370,
                        layout: 'fit',
                        title:  trlKwf('Categories'),
                        modal:  true,
                        items:  new Kwf.Auto.GridPanel({
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
