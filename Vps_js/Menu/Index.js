Vps.Menu.Index = function(renderTo, config)
{
    Ext.apply(this, config);
    this.events = {
        'loadpage' : true
    };
    this.tb = new Ext.Toolbar(renderTo);
    
    if (!this.controllerUrl) {
        this.controllerUrl = '/menu/';
    }
    this.controllerUrl += 'jsonData',

    Ext.Ajax.request({
        url: this.controllerUrl,
        params: config,
        success: this.loadMenu,
        scope: this
    });
    
    this.handleClick = function (o, e) {
        if (o.asEvent) {
            this.fireEvent('loadpage', {url: o.url, text: o.text});
        } else {
            location.href = o.url;
        }
    }
        
};

Ext.extend(Vps.Menu.Index, Ext.util.Observable,
{
    loadMenu: function(r)
    {
        var response = Ext.decode(r.responseText);
        for (var i=0; i<response.menus.length; i++) {
            var m = response.menus[i];
            if (m.url) {
                this.tb.add({
                    text: m.text,
                    handler: this.handleClick,
                    url: m.url,
                    asEvent: m.asEvent,
                    scope: this
                });
            } else {
                var menuItems = [];
                for (var j=0; j<m.children.length; j++) {
                    menuItems.push({
                        text: m.children[j].text,
                        handler: this.handleClick,
                        url: m.children[j].url,
                        asEvent: m.children[j].asEvent,
                        scope: this
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
        if (response.showLogout) {
            this.tb.addButton({
                text: 'Logout',
                handler: function() {
                    var logoutForm = new Ext.BasicForm(Ext.get(document.body).createChild({tag: 'form'}));
                    logoutForm.submit({
                    url:'/login/jsonLogoutUser',
                    success:function(form, action) {
                        location.href = '/';
                        }
                    });
                }
            });
        }
    }
    
});
