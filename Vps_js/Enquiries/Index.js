
Ext.namespace("Vps.Enquiries");

Vps.Enquiries.Index = Ext.extend(Vps.Auto.GridPanel,
{
    initComponent: function() {
        // Edit form
        var panel = new Vps.Enquiries.ViewPanel({
            controllerUrl: this.controllerUrl,
            title: trlVps('Enquiry'),
            region: 'center'
        });

        // main grid
        this.grid = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            title: trlVps('Enquiries'),
            region: 'west',
            width: 550,
            split: true,
            bindings: [ panel ]
        });

        this.layout = 'border';
        this.items = [ this.grid, panel ];

        Vps.Enquiries.Index.superclass.initComponent.call(this);
    }
});

Ext.reg('vps.enquiries.index', Vps.Enquiries.Index);
