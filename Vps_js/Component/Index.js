Ext.namespace('Vps.Component');
Vps.Component.Index = function(renderTo, config)
{
    Ext.apply(this, config);

    Vps.mainLayout = new Vps.StandardLayout(renderTo, {menuConfig: {controllerUrl: '/admin/menu/'}});
    Vps.mainLayout.add('center', new Ext.ContentPanel('panel-welcome', {title: 'Welcome', autoCreate: true}));
    Ext.get('panel-welcome').dom.innerHTML = 'Willkommen bei VPS';

    Vps.menu.on('menuevent', this.onMenuEvent, this)
}

Ext.extend(Vps.Component.Index, Ext.util.Observable,
{
    onMenuEvent : function(o)
    {
        var cls = eval(o.commandClass);
        if (cls.prototype.getPanel) {
            var page = new cls(Vps.mainLayout.el.createChild(), o.config);
            var panel = page.getPanel();
        } else {
            var panel = new Ext.ContentPanel('contentPanel', {autoCreate:true, autoScroll: true, fitToFrame: true});
            component = new cls('contentPanel', o.config);
        }
        var region = Vps.mainLayout.getRegion('center');
        region.remove(region.getActivePanel(), false);
        region.add(panel);
    }
}
)
