Ext2.namespace('Kwf.User.Login');
Kwf.User.Login.Dialog = Ext2.extend(Ext2.Window,
{
    initComponent: function()
    {
        this.height = 275+50;
        this.width = 310;
        this.modal = true;
        this.title = trlKwf('Login');
        this.resizable = false;
        this.closable = true;
        this.layout = 'border';
        this.loginPanel = new Ext2.Panel({
            baseCls: 'x2-plain',
            region: 'center',
            border: false,
            height: 120,
            html: '<iframe scrolling="no" src="/kwf/user/login/show-form" width="100%" '+
                    'height="100%" style="border: 0px"></iframe>'
        });
        this.lostPasswordButton = new Ext2.Button({
            text    : trlKwf('Lost password?'),
            style   : 'position: absolute; z-index: 500000; margin-top: -40px; margin-left: -270px;',
            handler : this.lostPassword,
            scope   : this,
            hidden  : true
        });
        this.loginPanelContainer = new Ext2.Panel({
            baseCls: 'x2-plain',
            region: 'center',
            border: false,
            layout: 'fit',
            height: 120,
            buttons: [
                this.lostPasswordButton
            ]
        });
        this.redirectsPanel = new Ext2.Panel({
            baseCls: 'x2-plain',
            region: 'south',
            height: 100,
            border: false
        });

        this.items = [{
            baseCls: 'x2-plain',
            cls: 'kwf-login-header',
            region: 'north',
            height: 80,
            autoLoad: '/kwf/user/login/header',
            border: false
        },{
            region: 'center',
            baseCls: 'x2-plain',
            border: false,
            items: [{
                baseCls: 'x2-plain',
                region: 'north',
                height: 40,
                bodyStyle: 'padding: 10px;',
                html: this.message,
                border: true
            }, this.loginPanelContainer,
               this.redirectsPanel
            ]
        }, ];

        Ext2.Ajax.request({
            url: '/kwf/user/login/json-get-auth-methods',
            success: function(response, options, r) {
                if (r.showPassword) {
                    this.lostPasswordButton.show();
                    this.loginPanelContainer.add(this.loginPanel);
                    this.loginPanelContainer.doLayout();
                }
                if (r.redirects.length) {
                    var tpl = new Ext2.XTemplate([
                        '<ul>',
                        '<tpl for=".">',
                            '<li>',
                            '<form>',
                            '<tpl for="formOptionsHtml">',
                                '<tpl if="type == \'select\'">',
                                    '<select name="{name}">',
                                    '<tpl for="values">',
                                        '<option value="{value}">{name}</option>',
                                    '</tpl>',
                                    '</select>',
                                '</tpl>',
                            '</tpl>',
                            '<a href="{url:htmlEncode}">',
                            '<tpl if="icon">',
                                '<img src="{icon}" />',
                            '</tpl>',
                            '<tpl if="!icon">',
                                '{name:htmlEncode}',
                            '</tpl>',
                            '</a>',
                            '</form>',
                            '</li>',
                        '</tpl>',
                        '</ul>'
                    ]);
                    tpl.overwrite(this.redirectsPanel.body, r.redirects);
                    this.redirectsPanel.body.select('a').each(function(a) {
                        a.on('click', function(ev) {
                            ev.preventDefault();
                            window.ssoCallback = (function() {
                                this.afterLogin();
                                delete window.ssoCallback;
                            }).bind(this);
                            var values = Ext2.lib.Ajax.serializeForm(Ext2.fly(ev.getTarget()).parent('form').dom);
                            var href = Ext2.fly(ev.getTarget()).parent('a').dom.href;
                            href += '&redirect=jsCallback&'+values;
                            window.open(href, 'sso', 'width=800,height=600');
                        }, this);
                    }, this);
                } else {
                    this.setHeight(275);
                }
            },
            scope: this
        });

        this.loginPanel.on('render', function(panel) {
            var frame = this.loginPanel.body.first('iframe');
            // IE sux :)
            // Das direkte this.onLoginLoad() in der nächsten Zeile muss wegen IE sein
            // da der das tlw. direkt im cache hat und das frame.onLoad nicht mitkriegt
            this.onLoginLoad();
            Ext2.EventManager.on(frame, 'load', this.onLoginLoad, this);
        }, this, { delay: 1 });


        Kwf.User.Login.Dialog.superclass.initComponent.call(this);
    },

    _getDoc: function() {
        var frame = this.loginPanel.body.first('iframe');
        if(Ext2.isIE){
            return frame.dom.contentWindow.document;
        } else {
            return (frame.dom.contentDocument || window.frames[id].document);
        }
    },

    onLoginLoad : function() {
        var doc = this._getDoc();

        if(doc && doc.body){
            if (doc.body.innerHTML.match(/successful/)) {
                this.afterLogin();
            } else if (doc.getElementsByName('username').length >= 1) {
                if (doc.activeElement && doc.activeElement.tagName.toLowerCase() != 'input') { //only focus() if not password has focus (to avoid users typing their password into username)
                    doc.getElementsByName('username')[0].focus();
                }
            }
        }
    },

    afterLogin: function()
    {
        this.hide();
        if (this.location) {
            location.href = this.location;
        } else {
            if (Kwf.menu) Kwf.menu.reload();
            if (this.success) {
                Ext2.callback(this.success, this.scope);
            }
        }
    },

    lostPassword: function() {
        location.href = '/kwf/user/login/lost-password';
    },

    showLogin: function() {
        this.show();
    }
});

