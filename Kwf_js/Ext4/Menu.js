Ext4.define('Kwf.Ext4.Menu', {
    extend: 'Ext.toolbar.Toolbar',
    uses: [
        'Ext.toolbar.Fill',
        'Ext.toolbar.Spacer'
    ],
    cls: 'kwf-ext4-menu',

    controllerUrl: '/kwf/user/menu',
    changeUserTpl: ['<tpl for=".">',
                        '<div class="x4-combo-list-item changeuser-list-item<tpl if="locked != 0"> changeuser-locked</tpl>">',
                            '<h3>{lastname}&nbsp;{firstname}</h3>',
                            '{email} <span class="changeuser-role">({role})</span>',
                        '</div>',
                      '</tpl>'],
    initComponent : function()
    {
        this.addEvents(
            'menuevent'
        );
        this.callParent(arguments);

    },
    afterRender: function() {
        this.callParent(arguments);
        this.reload();
    },
    reload: function()
    {
        Ext4.Ajax.request({
            url: this.controllerUrl+'/json-data',
            method: 'GET',
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
                var menu = new Ext4.menu.Menu({
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
                    cb();
//                     if (!Kwf.currentViewport || !Kwf.currentViewport.mabySubmit) {
//                         cb();
//                     } else {
//                         if (Kwf.currentViewport.mabySubmit({
//                             callback: cb,
//                             scope: this
//                         })) {
//                             cb();
//                         }
//                     }
                };
                subMenu.scope = this;
            } else if (m.type == 'separator') {
                subMenu = '-';
            } else {
                throw "unknown menu-type: "+m.type;
            }

            menuItems.push(subMenu);
        }
        return menuItems;
    },
    loadMenu: function(response, options)
    {
        var result = Ext4.JSON.decode(response.responseText);

        this.items.each(function(i) {
            i.destroy();
        });
        var menuItems = this._processMenus(result.menus);
        Ext4.each(menuItems, function(menuItem) {
            if (menuItem.icon && menuItem.text) {
                menuItem.cls = 'x4-btn-text-icon';
            } else if (menuItem.icon) {
                menuItem.cls = 'x4-btn-icon';
            }
            this.add(menuItem);
        }, this);
        this.add(new Ext4.toolbar.Fill());
        this.loadingAnim = this.add({
            xtype: 'component',
            cls: Ext4.baseCSSPrefix + 'mask-msg-text ext4-maincontroller-loading',
            height: 16,
            width: 16,
            renderTpl: '<div class=\"icon\"></div>',
            style: 'display:none'
        });

        /*
        TODO: port rest of this file to ext4

        this.showUserMenu = new Ext4.Button({
            id: 'userMenu',
            tooltip: trlKwf('Show User Menu'),
            cls: 'x4-btn-icon',
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
                    url: '/kwf/user/changeUser/json-data'
                },
                mode: 'remote',
                editable: true,
                forceSelection: true,
                pageSize: 10,
                triggerAction: 'all',
                width: 120,
                maxHeight: 350,
                listWidth: 280,
                tpl: new Ext4.XTemplate(
                        this.changeUserTpl
                      )
            });
            changeUser.on('render', function(combo) {
                combo.setRawValue(result.fullname);
            }, this, {delay: 10});
            changeUser.on('select', function(combo, record, index) {
                Ext4.Ajax.request({
                    url: '/kwf/user/changeUser/json-change-user',
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

        this.userToolbar = new Ext4.Toolbar({
            renderTo: this.el,
            style: 'position:absolute;right:0'
        });

        if (result.fullname && result.userSelfControllerUrl) {
            this.userToolbar.add({
                id: 'currentUser',
                text: result.fullname,
                cls: 'x4-btn-text-icon',
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
        */
        if (result.showLogout) {
            this.add({
                cls: 'x4-btn-icon',
                tooltip: trlKwf('Logout'),
                icon: '/assets/silkicons/door_out.png',
                handler: function() {
                    Ext4.Ajax.request({
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
        /*
        this.userToolbar.add({
            cls: 'x4-btn-icon',
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
                cls: 'x4-btn-icon',
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
                    cls: 'x4-btn-text-icon',
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
                cls: 'x4-btn-icon',
                icon: '/assets/silkicons/world.png',
                menu: new Ext4.menu.Menu({
                    items: frontendItems
                })
            });
        }
        */
    }
});
