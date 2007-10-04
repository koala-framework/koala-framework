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
                                var dlg = new c(o.commandConfig);
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
                            Vps.currentViewport.remove(Vps.currentViewport.items.item('mainPanel'));
                            var c = eval(o.commandClass);
                            var panel = new c(o.commandConfig);
                            panel.region = 'center';
                            panel.id = 'mainPanel';
                            Vps.currentViewport.add(panel);
                            Vps.currentViewport.layout.rendered = false;
                            Vps.currentViewport.doLayout();
                };
                subMenu.scope = this;
                subMenu.commandClass = m.commandClass;
                subMenu.commandConfig = m.commandConfig;
            } else if (m.type == 'separator') {
                subMenu = '-';
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
            this.render();
            this.items.clear();
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

            //button (in oberster ebene) kann kein href, darum mit handler faken
            if (menuItem.href) {
                menuItem.handler = function(menu) {
                    location.href = menu.href;
                }
            }
            this.add(menuItem);
        }, this);

        this.add(new Ext.Toolbar.Fill());
        if (response.authData && response.authData.realname) {
            this.add({
                text: response.authData.realname,
                cls: 'x-btn-text-icon',
                icon: '/assets/vps/images/silkicons/user.png',
                handler: function() {
                    //todo: display user settings dialog
                },
                scope: this
            });
        }
        if (response.showLogout) {
            this.add({
                text: 'Logout',
                handler: function() {
                    Ext.Ajax.request({
                        url : '/login/jsonLogoutUser',
                        success : function(form, action) {
                            location.reload();
                        },
                        scope: this
                    });
                },
                scope: this
            });
        }
    }
});
