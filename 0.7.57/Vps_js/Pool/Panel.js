Ext.namespace('Vps.Pool');
Vps.Pool.Panel = Ext.extend(Ext.Panel,
{
    initComponent : function()
    {
        var form = new Vps.Auto.GridPanel({
            controllerUrl   : '/vps/pool/pool',
            region          : 'center'
        });

        var grid = new Vps.Auto.GridPanel({
            controllerUrl   : '/vps/pool/pools',
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
        Clubs.superclass.initComponent.call(this);
    }
});
