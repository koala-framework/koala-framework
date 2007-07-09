Vps.StandardLayout = function(renderTo, config)
{
    if(!config) config = {};

    Ext.applyIf(config, {
            north: {split: false, initialSize: 30},
            center: { autoScroll: false },
            createWorkspaceContainer: true
        });
    Vps.StandardLayout.superclass.constructor.call(this, renderTo, config);
    this.beginUpdate();

    this.add('north', new Ext.ContentPanel('menusContainer', {autoCreate: true, fitToFrame:true, closable:false}));
    new Vps.Menu.Index('menusContainer');
    
    this.endUpdate();
//     if (config.createWorkspaceContainer) {
//         this.add('center', new Ext.ContentPanel('workspaceContainer', {autoCreate: true, fitToFrame:true, closable:false}));
//     }

};

Ext.extend(Vps.StandardLayout, Ext.BorderLayout,
{

});
