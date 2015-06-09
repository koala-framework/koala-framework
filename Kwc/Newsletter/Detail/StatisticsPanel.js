Ext2.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.StatisticsPanel = Ext2.extend(Kwf.Binding.AbstractPanel, {
    layout: 'border',

    initComponent: function() {
        var clicks = new Kwf.Auto.GridPanel({
            region: 'center',
            baseParams: this.baseParams,
            controllerUrl: this.clicksControllerUrl
        });
        var links = new Kwf.Auto.GridPanel({
            region: 'west',
            controllerUrl:  this.linksControllerUrl,
            baseParams: this.baseParams,
            bindings: [ clicks ]
        });
        this.items = [links, clicks];
        Kwc.Newsletter.Detail.StatisticsPanel.superclass.initComponent.call(this);
    }
});
Ext2.reg('kwc.newsletter.statistics', Kwc.Newsletter.Detail.StatisticsPanel);
