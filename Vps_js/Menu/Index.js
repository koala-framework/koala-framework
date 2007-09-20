Ext.namespace('Vps.Menu');

Vps.Menu.Index = Ext.extend(Ext.Toolbar, {
    userRole: null,
    initComponent : function()
    {
        this.addEvents({
            'menuevent' : true
        });

        if (!this.controllerUrl) {
            this.controllerUrl = '/menu/';
        }
        this.reload();

        Vps.Menu.Index.superclass.initComponent.call(this);
    },
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
            var subMenu = m.menuConfig;
            if (m.type == 'dropdown') {
                var childMenuItems = this._processMenus(m.children);
                var menu = new Ext.menu.Menu({
                    items: childMenuItems
                });
                subMenu.menu = menu;
            } else if (m.type == 'url') {
                subMenu.href = m.url;
            } else if (m.type == 'event') {
                subMenu.handler = function(o) {
                                if(o.eventConfig && !o.eventConfig.title) o.eventConfig.title = o.text;
                                this.fireEvent('menuevent', o.eventConfig);
                            };
                subMenu.scope = this;
                subMenu.eventConfig = m.eventConfig;
            } else if (m.type == 'commandDialog') {
                subMenu.handler = function(o) {
                                var c = eval(o.commandClass);
                                var dlg = new c(null, o.commandConfig);
                                dlg.show();
                            };
                subMenu.scope = this;
                subMenu.commandClass = m.commandClass;
                subMenu.commandConfig = m.commandConfig;
                if (m.command != undefined) {
                    eval(m.command + '();');
                }
            } else if (m.type == 'command') {
                subMenu.handler = function(o) {
                                if (o.object && o.object.activate) {
                                    o.object.activate();
                                } else {
                                    var c = eval(o.commandClass);
                                    o.object = new c(null, o.commandConfig);
                                }
                            };
                subMenu.scope = this;
                subMenu.commandClass = m.commandClass;
                subMenu.commandConfig = m.commandConfig;
            } else {
                throw "unknown menu-type: "+m.type;
            }
            menuItems.push(subMenu);
        }
        return menuItems;
    },
    loadMenu: function(r)
    {
        if (this.items.getCount() > 0) {
            //tolbar komplett löschen und neu erstellen
            this.destroy();
            this.render(this.renderTo);
        }
        var response = Ext.decode(r.responseText);
        Vps.Menu.userRole = response.userRole;
        var menuItems = this._processMenus(response.menus);
        menuItems.each(function(menuItem) {
            if (menuItem.icon && menuItem.text) {
                menuItem.cls = 'x-btn-text-icon';
            } else if (menuItem.icon) {
                menuItem.cls = 'x-btn-icon';
            }
            this.add(menuItem);
        }, this);

        if (response.showLogout) {
            this.add(new Ext.Toolbar.Fill());
            this.add({
                text: 'Logout',
                handler: function() {
                    Ext.Ajax.request({
                        url : '/login/jsonLogoutUser',
                        success : function(form, action) {
                            this.reload();
                        },
                        scope: this
                    });
                },
                scope: this
            });
        }
    }
});
