Ext.namespace('Kwf.Component.Frontend');
Kwf.Component.Frontend.Index = function(renderTo, config)
{
    if (config.menu) {
        var menu = new Kwf.Menu.Index(Ext.DomHelper.insertFirst(document.body, '<div style="position: absolute; z-index:10" \/>', true), {controllerUrl: '/admin/menu'});
        menu.on('menuevent', this.loadComponent, this);
    }
    if (config.fe) {
        var fe = new Kwf.Component.FrontendEditing.Index(renderTo, config);
    }
};

Ext.extend(Kwf.Component.Frontend.Index, Ext.util.Observable,
{
    loadComponent : function(data) {
        document.location.href = '/admin/pages/?url=' + data.url + '&name=' + data.name;
    }
});
