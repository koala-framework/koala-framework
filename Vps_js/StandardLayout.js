Vps.StandardLayout = function(renderTo, config)
{
    if(!config) config = {};

    Ext.applyIf(config, {
            north: {split: false, initialSize: 30},
            center: { autoScroll: false, closeOnTab: true},
            createWorkspaceContainer: true
        });
    Vps.StandardLayout.superclass.constructor.call(this, renderTo, config);
    this.beginUpdate();

    this.add('north', new Ext.ContentPanel('menusContainer', {autoCreate: true, fitToFrame:true, closable:false}));
    
    //global verfügbare variable für menü (um es zB neuladen zu können)
    Vps.menu = new Vps.Menu.Index('menusContainer', config.menuConfig);
    
    this.endUpdate();
};

Ext.extend(Vps.StandardLayout, Ext.BorderLayout,
{

});
