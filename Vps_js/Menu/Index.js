Vps.Menu.Index = function(renderTo, config)
{
    Ext.apply(this, config);

    this.tb = new Ext.Toolbar(renderTo);
    var conn = new Vps.Connection();
    conn.request({
        url: '/admin/menu/ajaxData',
        success: this.loadMenu,
        scope: this
    });
};

Ext.extend(Vps.Menu.Index, Ext.util.Observable,
{
    loadMenu: function(response)
    {
        for (var i=0; i<response.menus.length; i++) {
            var m = response.menus[i];
            if (m.url) {
                this.tb.add({
                    text: m.text,
                    handler: function() {
                        location.href = this.url;
                    },
                    url: m.url
                });
            } else {
                var menuItems = [];
                for (var j=0; j<m.children.length; j++) {
                    menuItems.push({
                        text: m.children[j].text,
                        handler: function() {
                            location.href = this.url;
                        },
                        url: m.children[j].url
                    });
                }
                var menu = new Ext.menu.Menu({
                    id: 'mainMenu',
                    items: menuItems
                });
                this.tb.add({
                    text: m.text,
                    menu: menu
                });
            }
        }

        this.tb.addSpacer().getEl().parentNode.style.width = '100%';
        this.tb.addButton({
                text: 'Logout',
                handler: function() {
                    var logoutForm = new Ext.BasicForm(Ext.get(document.body).createChild({tag: 'form'}));
                    logoutForm.submit({
                        url:'/user/ajaxLogout',
                        success:function(form, action) {
                            location.href = '/';
                        }
                    });
                }
            }
          );
    }
});
