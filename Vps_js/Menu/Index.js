Vps.Menu.Index = function(renderTo, config)
{
    Ext.apply(this, config);
    this.renderTo = renderTo;
    this.events = {
        'menuevent' : true
    };
    this.tb = new Ext.Toolbar(renderTo);
    
    if (!this.controllerUrl) {
        this.controllerUrl = '/menu/';
    }
    this.reload();
};

Ext.extend(Vps.Menu.Index, Ext.util.Observable,
{
    userRole: null,
    reload: function()
    {
        Ext.Ajax.request({
            url: this.controllerUrl+'jsonData',
            params: this.params,
            success: this.loadMenu,
            scope: this
        });
    },
    _processMenus: function(menus)
    {
        var menuItems = [];
        for (var i=0; i<menus.length; i++) {
            var m = menus[i];
            if (m.type == 'dropdown') {
                var childMenuItems = this._processMenus(m.children);
                var menu = new Ext.menu.Menu({
                    items: childMenuItems
                });
                menuItems.push({
                    text: m.text,
                    menu: menu
                });
            } else if (m.type == 'url') {
                menuItems.push({
                    text: m.text,
                    handler: function(o) {
                        location.href = o.url;
                    },
                    url: m.url
                });
            } else if (m.type == 'event') {
                menuItems.push({
                    text: m.text,
                    handler: function(o) {
                        if(!o.config.title) o.config.title = o.text;
                        this.fireEvent('menuevent', o.config);
                    },
                    scope: this,
                    config: m.config
                });
            } else if (m.type == 'commandDialog') {
                menuItems.push({
                    text: m.text,
                    handler: function(o) {
                        var c = eval(o.commandClass);
                        var dlg = new c(null, o.config);
                        dlg.show();
                    },
                    scope: this,
                    commandClass: m.commandClass,
                    config: m.config
                });
            } else if (m.type == 'command') {
                menuItems.push({
                    text: m.text,
                    handler: function(o) {
                        if (o.object && o.object.activate) {
                            o.object.activate();
                        } else {
                            var c = eval(o.commandClass);
                            o.object = new c(null, o.config);
                        }
                    },
                    scope: this,
                    commandClass: m.commandClass,
                    config: m.config
                });
            }
        }
        return menuItems;
    },
    loadMenu: function(r)
    {
        if (this.tb.items.getCount() > 0) {
            //tolbar komplett löschen und neu erstellen
            this.tb.destroy();
            this.tb.render(this.renderTo);
        }
        var response = Ext.decode(r.responseText);
        Vps.Menu.userRole = response.userRole;
        var menuItems = this._processMenus(response.menus);
        menuItems.each(function(menuItem) {
            this.tb.add(menuItem);
        }, this);

        if (response.showLogout) {
            this.tb.addButton(new Ext.Toolbar.Fill());
            this.tb.addButton({
                text: 'Logout',
                handler: function() {
                    Ext.Ajax.request({
                        url : '/login/jsonLogoutUser',
                        success : function(form, action) {
                            location.href = '/';
                        }
                    });
                }
            });
        }
    }
    
});
