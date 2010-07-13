Ext.namespace('Vpc.NewsletterCategory');
Vpc.NewsletterCategory.Plugin = function(config) {
    Ext.apply(this, config);
};
Ext.extend(Vpc.NewsletterCategory.Plugin, Ext.util.Observable,
{
    init: function(panel)
    {
		panel.on('beforerendergrid', function(grid) {
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
                            baseParams: panel.getBaseParams()
                        })
                    }, this);
                    dlg.show();
                },
                scope   : this
            });
        }, this);
    }
});
