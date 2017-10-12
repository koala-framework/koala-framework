Ext2.namespace('Kwf.Menu');

Kwf.Menu.Index = Ext2.extend(Ext2.Toolbar,
{
    controllerUrl: '/kwf/user/menu',
    changeUserUrl: '/kwf/user/changeUser',
    changeUserTpl: ['<tpl for=".">',
                        '<div class="x2-combo-list-item changeuser-list-item">',
                            '<h3>{lastname:htmlEncode}&nbsp;{firstname:htmlEncode}</h3>',
                            '{email:htmlEncode} <span class="changeuser-role">({role:htmlEncode})</span>',
                        '</div>',
                      '</tpl>'],
    changeUserHeight: 350,
    changeUserWidth: 280,

    initComponent : function()
    {
        this.addEvents(
            'menuevent'
        );
        Kwf.Menu.Index.superclass.initComponent.call(this);

    },
    afterRender: function() {
        Kwf.Menu.Index.superclass.afterRender.call(this);
        this.reload();
    },
    reload: function()
    {
        Ext2.Ajax.request({
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
                var menu = new Ext2.menu.Menu({
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
                    if (!Kwf.currentViewport || !Kwf.currentViewport.mabySubmit) {
                        cb();
                    } else {
                        if (Kwf.currentViewport.mabySubmit({
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
                    Kwf.currentViewport.remove(Kwf.currentViewport.items.item('mainPanel'));
                    var c = eval(o.commandClass);
                    var panel = new c(o.commandConfig);
                    panel.region = 'center';
                    panel.id = 'mainPanel';
                    Kwf.currentViewport.add(panel);
                    Kwf.currentViewport.layout.rendered = false;
                    Kwf.currentViewport.doLayout();
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
                menuItem.cls = 'x2-btn-text-icon';
            } else if (menuItem.icon) {
                menuItem.cls = 'x2-btn-icon';
            }
            this.add(menuItem);
        }, this);

        this.add(new Ext2.Toolbar.Fill());

        this.showUserMenu = new Ext2.Button({
            id: 'userMenu',
            tooltip: trlKwf('Show User Menu'),
            cls: 'x2-btn-icon',
            icon: '/assets/silkicons/bullet_arrow_down.png',
            handler: function() {
                if (!this.userToolbar.isVisible()) {
                    this.showUserMenu.btnEl.setStyle('background-image', 'url(/assets/silkicons/bullet_arrow_up.png)');
                    this.userToolbar.show();
                } else {
                    this.showUserMenu.btnEl.setStyle('background-image', 'url(/assets/silkicons/bullet_arrow_down.png)');
                    this.userToolbar.hide();
                }
            },
            scope: this
        });
        this.add(this.showUserMenu);

        if (result.changeUser) {
            var changeUser = new Kwf.Form.ComboBox({
                store: {
                    url: this.changeUserUrl+'/json-data'
                },
                mode: 'remote',
                editable: true,
                forceSelection: true,
                pageSize: 10,
                triggerAction: 'all',
                width: 120,
                maxHeight: this.changeUserHeight,
                listWidth: this.changeUserWidth,
                tpl: new Ext2.XTemplate(
                        this.changeUserTpl
                      )
            });
            changeUser.on('render', function(combo) {
                combo.setRawValue(result.fullname);
            }, this, {delay: 10});
            changeUser.on('select', function(combo, record, index) {
                Ext2.Ajax.request({
                    url: this.changeUserUrl+'/json-change-user',
                    params: { userId: record.id },
                    success: function() {
                        location.href = '/kwf/welcome';
                    },
                    scope: this
                });
            }, this);
            this.add(changeUser);
            this.add(' ');
            this.add(' ');
            this.add('-');
        }

        this.userToolbar = new Ext2.Toolbar({
            renderTo: this.el,
            style: 'position:absolute;right:0'
        });

        if (result.fullname && result.userSelfControllerUrl) {
            this.userToolbar.add({
                id: 'currentUser',
                text: Ext2.util.Format.htmlEncode(result.fullname),
                cls: 'x2-btn-text-icon',
                icon: '/assets/silkicons/user.png',
                disabled: !result.userId,
                handler: function() {
                    var dlg = new Kwf.Auto.Form.Window({
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
            this.userToolbar.add({
                cls: 'x2-btn-icon',
                tooltip: trlKwf('Logout'),
                icon: '/assets/silkicons/door_out.png',
                handler: function() {
                    Ext2.Ajax.request({
                        url : '/kwf/user/login/json-logout-user',
                        success : function(form, action) {
                            //nicht reload, weil user nach erneutem login vielleicht
                            //die aktuelle seite gar nicht mehr sehen darf
                            location.href = '/kwf/welcome';
                        },
                        scope: this
                    });
                },
                scope: this
            });
        }
        this.userToolbar.add({
            cls: 'x2-btn-icon',
            icon: '/assets/kwf/images/information.png',
            tooltip: trlKwf('Information'),
            handler: function() {
                var about = new Kwf.About();
                about.show();
            },
            scope: this
        });

        this.userToolbar.hide();


        if (result.frontendUrls.length == 1) {
            //single frontend urls
            this.add({
                tooltip: trlKwf('Open frontend in a new window'),
                cls: 'x2-btn-icon',
                icon: '/assets/silkicons/world.png',
                handler: function() {
                    window.open(result.frontendUrls[0].href);
                },
                scope: this
            });
        } else if (result.frontendUrls.length > 1) {
            //multiple frontend urls
            var frontendItems = [];
            result.frontendUrls.each(function(url) {
                frontendItems.push({
                    text: url.text,
                    cls: 'x2-btn-text-icon',
                    icon: '/assets/silkicons/world.png',
                    tooltip: trlKwf('Open frontend in a new window'),
                    handler: function(options) {
                        window.open(options.url.href);
                    },
                    url: url,
                    scope: this
                });
            }, this);
            this.add({
                tooltip: trlKwf('Open frontend in a new window'),
                cls: 'x2-btn-icon',
                icon: '/assets/silkicons/world.png',
                menu: new Ext2.menu.Menu({
                    items: frontendItems
                })
            });
        }
    }
});
Ext2.reg('kwf.menu', Kwf.Menu.Index);
