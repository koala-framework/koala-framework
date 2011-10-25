Ext.namespace("Kwf.Redirects");
Kwf.Redirects.Index = Ext.extend(Ext.Panel, {
    initComponent: function() {
        var form = new Kwf.Auto.FormPanel({
            controllerUrl: '/kwf/redirects/redirect',
            region: 'center'
        });
        var grid = new Kwf.Auto.GridPanel({
            controllerUrl: '/kwf/redirects/redirects',
            region: 'west',
            split: true,
            width: 500,
            bindings: [ form ]
        });



        this.layout = 'border';
        this.items = [ grid, form ];

        Kwf.Redirects.Index.superclass.initComponent.call(this);
    }
});
