Ext.namespace('Vps.Menu');

Vps.Menu.Index = Ext.extend(Ext.Toolbar,
{
    controllerUrl: '/vps/user/menu',
	changeUserTpl: ['<tpl for=".">',
                        '<div class="x-combo-list-item changeuser-list-item<tpl if="locked != 0"> changeuser-locked</tpl>">',
                            '<h3>{lastname}&nbsp;{firstname}</h3>',
                            '{email} <span class="changeuser-role">({role})</span>',
                        '</div>',
                      '</tpl>'],
    tplDataControllerUrl: '/vps/user/changeUser/json-data',

    initComponent : function()
    {
        this.addEvents(
            'menuevent'
        );
        Vps.Menu.Index.superclass.initComponent.call(this);

    },
    afterRender: function() {
        Vps.Menu.Index.superclass.afterRender.call(this);
        this.reload();
    },
    reload: function()
    {
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-data',
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
                subMenu.handler = function(o, e) {
                    e.stopEvent();
                    var cb = function() {
                        if (top != window) {
                            top.location.href = o.href;
                        } else {
                            location.href = o.href;
                        }
                    };
                    if (!Vps.currentViewport || !Vps.currentViewport.mabySubmit) {
                        cb();
                    } else {
                        if (Vps.currentViewport.mabySubmit({
                            callback: cb,
                            scope: this
                        })) {
                            cb();
                        }
                    }
                };
                subMenu.scope = this;
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
    loadMenu: function(response, options, result)
    {
        this.items.each(function(i) {
            i.destroy();
        });
        var menuItems = this._processMenus(result.menus);
        menuItems.each(function(menuItem) {
            if (menuItem.icon && menuItem.text) {
                menuItem.cls = 'x-btn-text-icon';
            } else if (menuItem.icon) {
                menuItem.cls = 'x-btn-icon';
            }
            this.add(menuItem);
        }, this);

        this.add(new Ext.Toolbar.Fill());

        if (result.changeUser) {
            var changeUser = new Vps.Form.ComboBox({
                store: {
                    url: this.tplDataControllerUrl
                },
                mode: 'remote',
                editable: true,
                forceSelection: true,
                pageSize: 10,
                triggerAction: 'all',
                width: 120,
                maxHeight: 350,
                listWidth: 280,
                tpl: new Ext.XTemplate(
				        this.changeUserTpl
                      )
            });
            changeUser.on('render', function(combo) {
                combo.setRawValue(result.fullname);
            }, this, {delay: 10});
            changeUser.on('select', function(combo, record, index) {
                Ext.Ajax.request({
                    url: '/vps/user/changeUser/json-change-user',
                    params: { userId: record.id },
                    success: function() {
                        location.href = '/vps/welcome';
                    },
                    scope: this
                });
            }, this);
            this.add(changeUser);
        }

        if (result.fullname && result.userSelfControllerUrl) {
            this.add({
                id: 'currentUser',
                text: result.fullname,
                cls: 'x-btn-text-icon',
                icon: '/assets/silkicons/user.png',
                handler: function() {
                    var dlg = new Vps.Auto.Form.Window({
                        formConfig: {
                            controllerUrl: result.userSelfControllerUrl
                        }
                    });
                    dlg.on('datachange', function() {
                        this.reload();
                    }, this);
                    dlg.showEdit(result.userId);
                },
                scope: this
            });
        }
        if (result.showLogout) {
            this.add({
                cls: 'x-btn-icon',
                tooltip: trlVps('Logout'),
                icon: '/assets/silkicons/door_out.png',
                handler: function() {
                    Ext.Ajax.request({
                        url : '/vps/user/login/json-logout-user',
                        success : function(form, action) {
                            //nicht reload, weil user nach erneutem login vielleicht
                            //die aktuelle seite gar nicht mehr sehen darf
                            location.href = '/vps/welcome';
                        },
                        scope: this
                    });
                },
                scope: this
            });
        }
        this.add({
            cls: 'x-btn-icon',
            icon: '/assets/vps/images/information.png',
            tooltip: trlVps('Information'),
            handler: function() {
                var about = new Vps.About();
                about.show();
            },
            scope: this
        });
        if (result.hasFrontend) {
            this.add({
                tooltip: trlVps('Open frontend in a new window'),
                cls: 'x-btn-icon',
                icon: '/assets/silkicons/world.png',
                handler: function() {
                    window.open('/');
                },
                scope: this
            });
        }

        if (Vps.Debug.showMenu) {
            this.add('-');
            this.add({
                cls: 'x-btn-icon',
                icon: '/assets/silkicons/bug.png',
                menu: new Vps.Debug.Menu()
            });
        } else if (Vps.Debug.showActivator) {
            this.add('-');
            this.add({
                tooltip: 'Activate Debugging',
                cls: 'x-btn-icon',
                icon: '/assets/silkicons/bug.png',
                handler: function() {
                    location.href = '/vps/debug/activate?url=' + location.href;
                }
            });
        }
    }
});
Ext.reg('vps.menu', Vps.Menu.Index);
