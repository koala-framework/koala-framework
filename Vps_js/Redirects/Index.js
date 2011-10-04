Ext.namespace("Vps.Redirects");
Vps.Redirects.Index = Ext.extend(Ext.Panel, {
    initComponent: function() {
        var form = new Vps.Auto.FormPanel({
            controllerUrl: '/vps/redirects/redirect',
            region: 'center'
        });
        var grid = new Vps.Auto.GridPanel({
            controllerUrl: '/vps/redirects/redirects',
            region: 'west',
            split: true,
            width: 500,
            bindings: [ form ]
        });



        this.layout = 'border';
        this.items = [ grid, form ];

        Vps.Redirects.Index.superclass.initComponent.call(this);
    }
});
