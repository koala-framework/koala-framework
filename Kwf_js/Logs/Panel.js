Ext.namespace("Kwf.Logs");
Kwf.Logs.Panel = Ext.extend(Ext.Panel, {
    initComponent: function() {
        var form = new Kwf.Auto.FormPanel({
            controllerUrl: this.formControllerUrl,
            region: 'east',
            split: true,
            width: 550
        });
        var grid = new Kwf.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            region: 'center',
            bindings: [ form ]
        });



        this.layout = 'border';
        this.items = [ grid, form ];

        Kwf.Logs.Panel.superclass.initComponent.call(this);
    }
});

Ext.reg('kwf.logs.panel', Kwf.Logs.Panel);
