Ext.namespace('Vps.Component.Frontend');
Vps.Component.Frontend.Index = function(renderTo, config)
{
    if (config.menu) {
        var menu = new Vps.Menu.Index(Ext.DomHelper.insertFirst(document.body, '<div style="position: absolute; z-index:10" \/>', true), {controllerUrl: '/admin/menu'});
        menu.on('menuevent', this.loadComponent, this);
    }
    if (config.fe) {
        var fe = new Vps.Component.FrontendEditing.Index(renderTo, config);
    }
};

Ext.extend(Vps.Component.Frontend.Index, Ext.util.Observable,
{
    loadComponent : function(data) {
        document.location.href = '/admin/pages/?url=' + data.url + '&name=' + data.name;
    }
});
