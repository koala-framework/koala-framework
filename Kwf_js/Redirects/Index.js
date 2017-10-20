Ext2.namespace("Kwf.Redirects");
Kwf.Redirects.Index = Ext2.extend(Ext2.Panel, {
    initComponent: function() {
        var form = new Kwf.Auto.FormPanel({
            controllerUrl: KWF_BASE_URL+'/kwf/redirects/redirect',
            region: 'center'
        });
        var grid = new Kwf.Auto.GridPanel({
            controllerUrl: KWF_BASE_URL+'/kwf/redirects/redirects',
            region: 'west',
            split: true,
            width: 550,
            bindings: [ form ]
        });



        this.layout = 'border';
        this.items = [ grid, form ];

        Kwf.Redirects.Index.superclass.initComponent.call(this);
    }
});
