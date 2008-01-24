Ext.namespace('Vpc.News.Categories');
Vpc.News.Categories.Plugins = Ext.extend(Ext.util.Observable,
{
    init: function(newsPanel)
    {
        newsPanel.on('beforerendergrid', function(grid)
        {
            grid.getTopToolbar().add({
                text    : 'Categories',
                handler : function(o, p) {
                    var dlg = new Ext.Window({
                        width:  450,
                        height: 370,
                        layout: 'fit',
                        title:  'News Categories',
                        modal:  true,
                        items:  new Vps.Auto.GridPanel({
                            controllerUrl: '/admin/component/edit/Vpc_News_Categories_Controller',
                            baseParams: this.getBaseParams()
                        })
                    }, this);
                    dlg.show();
                },
                scope   : this
            });
        }, this);
    }
});
