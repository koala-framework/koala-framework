Ext.ns('Vpc.Newsletter.Detail');
Vpc.Newsletter.Detail.StatisticsPanel = Ext.extend(Vps.Binding.AbstractPanel, {
    layout: 'border',

    initComponent: function() {
        var clicks = new Vps.Auto.GridPanel({
            region: 'center',
            baseParams: this.baseParams,
            controllerUrl: this.clicksControllerUrl
        });
        var links = new Vps.Auto.GridPanel({
            region: 'west',
            controllerUrl:  this.linksControllerUrl,
            baseParams: this.baseParams,
            bindings: [ clicks ]
        });
        this.items = [links, clicks];
        Vpc.Newsletter.Detail.StatisticsPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.newsletter.statistics', Vpc.Newsletter.Detail.StatisticsPanel);
