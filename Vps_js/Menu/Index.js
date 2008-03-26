Ext.namespace('Vps.Menu');

Vps.Menu.Index = Ext.extend(Ext.Toolbar, {
    initComponent : function()
    {
        this.addEvents(
            'menuevent'
        );

        if (!this.controllerUrl) {
            this.controllerUrl = '/vps/user/menu';
        }
        this.reload();

        Vps.Menu.Index.superclass.initComponent.call(this);
    },
    reload: function()
    {
        Ext.Ajax.request({
            url: this.controllerUrl+'/jsonData',
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
                        location.href = o.href;
                    };
                    if (!Vps.currentViewport.mabySubmit) cb();
                    if (Vps.currentViewport.mabySubmit({
                        callback: cb,
                        scope: this
                    })) {
                        cb();
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
    loadMenu: function(r)
    {
        this.items.each(function(i) {
            i.destroy();
        });
        var response = Ext.decode(r.responseText);
        var menuItems = this._processMenus(response.menus);
        menuItems.each(function(menuItem) {
            if (menuItem.icon && menuItem.text) {
                menuItem.cls = 'x-btn-text-icon';
            } else if (menuItem.icon) {
                menuItem.cls = 'x-btn-icon';
            }
            this.add(menuItem);
        }, this);

        this.add(new Ext.Toolbar.Fill());

        if (response.changeUser) {
            var changeUser = new Vps.Form.ComboBox({
                store: {
                    url: '/vps/user/changeUser/jsonData'
                },
                mode: 'remote',
                editable: false,
                triggerAction: 'all',
                width: 120,
                listWidth: 250,
                tpl: new Ext.XTemplate(
                      '<tpl for=".">',
                        '<div class="x-combo-list-item">',
                            '<h3>{name}</h3>',
                            '{email} {role}',
                        '</div>',
                      '</tpl>')
            });
            changeUser.on('render', function(combo) {
                combo.setRawValue(response.fullname);
            }, this);
            changeUser.on('select', function(combo, record, index) {
                Ext.Ajax.request({
                    url: '/vps/user/changeUser/jsonChangeUser',
                    params: { userId: record.id },
                    success: function() {
                        location.href = '/vps/welcome';
                    },
                    scope: this
                });
            }, this);
            this.add(changeUser);
        }

        if (response.fullname && response.userSelfControllerUrl) {
            this.add({
                text: response.fullname,
                cls: 'x-btn-text-icon',
                icon: '/assets/silkicons/user.png',
                handler: function() {
                    var dlg = new Vps.Auto.Form.Window({
                        width: 450,
                        height: 370,
                        formConfig: {
                            controllerUrl: response.userSelfControllerUrl
                        }
                    });
                    dlg.on('datachange', function() {
                        this.reload();
                    }, this);
                    dlg.showEdit(response.userId);
                },
                scope: this
            });
        }
        if (response.showLogout) {
            this.add({
                text: trlVps('Logout'),
                cls: 'x-btn-text-icon',
                icon: '/assets/silkicons/door_out.png',
                handler: function() {
                    Ext.Ajax.request({
                        url : '/vps/user/login/jsonLogoutUser',
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
            text: 'Info',
            cls: 'x-btn-text-icon',
            icon: '/assets/vps/images/information.png',
            handler: function() {
                var about = new Vps.About();
                about.show();
            },
            scope: this
        });
        if (Vps.debug) {
            this.add('-');
            this.add({
                text: 'Debug',
                cls: 'x-btn-text-icon',
                icon: '/assets/silkicons/bug.png',
                menu: [{
                    text: 'clear assets-cache',
                    icon: '/assets/silkicons/database_delete.png',
                    cls: 'x-btn-text-icon',
                    scope: this,
                    handler: function() {
                        Ext.Ajax.request({
                            url: this.controllerUrl+'/jsonClearAssetsCache',
                            success: function() {
                                Ext.Msg.alert('Clear Assets Cache', 'successfully cleared');
                            }
                        });
                    }
                },{
                    text: 'show debug console',
                    icon: '/assets/silkicons/bug.png',
                    cls: 'x-btn-text-icon',
                    scope: this,
                    handler: function() {
                        Ext.log();
                    }
                }],
                scope: this
            });
        }
    }
});
