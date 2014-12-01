Ext2.namespace('Kwf.Pool');
Kwf.Pool.Panel = Ext2.extend(Ext2.Panel,
{
    initComponent : function()
    {
        var form = new Kwf.Auto.GridPanel({
            controllerUrl   : '/kwf/pool/pool',
            region          : 'center'
        });

        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/kwf/pool/pools',
            region          : 'west',
            width           : 200,
            split           : true,
            collapsible     : true,
            title           : 'Pools',
            bindings: [{
               item             : form,
               queryParam       : 'pool'
            }]
        });

        this.layout = 'border';
        this.items = [grid, form];
        Kwf.Pool.Panel.superclass.initComponent.call(this);
    }
});
