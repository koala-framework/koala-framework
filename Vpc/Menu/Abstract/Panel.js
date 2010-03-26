Ext.namespace('Vpc.Menu');
Vpc.Menu.Panel = Ext.extend(Vps.Binding.ProxyPanel,
{
    initComponent : function(test)
    {
        this.form = new Vps.Auto.FormPanel({
            controllerUrl: this.formControllerUrl,
            baseParams      : this.baseParams,
            region : 'center'
        });
        this.tree = new Vps.Auto.SyncTreePanel({
            controllerUrl   : this.controllerUrl,
            region          : 'west',
            minWidth        : 340,
            width           : 340,
            resizable       : true,
            split           : true,
            baseParams      : this.baseParams,
            bindings        : [this.form]
        });

        this.layout = 'border';
        this.items = [this.tree, this.form];
        this.proxyItem = this.tree;
        Vpc.Menu.Panel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.menu.panel', Vpc.Menu.Panel);