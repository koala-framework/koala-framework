
Ext2.namespace("Kwf.Enquiries");

Kwf.Enquiries.Index = Ext2.extend(Kwf.Auto.GridPanel,
{
    initComponent: function() {
        // Edit form
        var panel = new Kwf.Enquiries.ViewPanel({
            controllerUrl: this.controllerUrl,
            title: trlKwf('Enquiry'),
            region: 'center'
        });

        // main grid
        this.grid = new Kwf.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            title: trlKwf('Enquiries'),
            region: 'west',
            width: 550,
            split: true,
            bindings: [ panel ]
        });

        this.layout = 'border';
        this.items = [ this.grid, panel ];

        Kwf.Enquiries.Index.superclass.initComponent.call(this);
    }
});

Ext2.reg('kwf.enquiries.index', Kwf.Enquiries.Index);
