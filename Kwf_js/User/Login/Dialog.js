Ext.namespace('Kwf.User.Login');
Kwf.User.Login.Dialog = Ext.extend(Ext.Window,
{
    initComponent: function()
    {
        this.height = 255;
        this.width = 310;
        this.modal = true;
        this.title = trlKwf('Login');
        this.resizable = false;
        this.closable = true;
        this.layout = 'border';
        this.loginPanel = new Ext.Panel({
            baseCls: 'x-plain',
            region: 'south',
            border: false,
            height: 125,
            html: '<iframe scrolling="no" src="/kwf/user/login/show-form" width="100%" '+
                    'height="100%" style="border: 0px"></iframe>'
        });
        this.items = [{
            baseCls: 'x-plain',
            cls: 'kwf-login-header',
            region: 'north',
            height: 80,
            autoLoad: '/kwf/user/login/header',
            border: false
        },{
            baseCls: 'x-plain',
            region: 'center',
            bodyStyle: 'padding: 10px;',
            html: this.message,
            border: false
        }, this.loginPanel];

        this.buttons = [
            new Ext.Button({
                text    : trlKwf('Lost password?'),
                style   : 'position: absolute; z-index: 500000; margin-top: -40px; margin-left: -270px;',
                handler : this.lostPassword,
                scope   : this
            })
        ];

        this.loginPanel.on('render', function(panel) {
            var frame = this.loginPanel.body.first('iframe');
            // IE sux :)
            // Das direkte this.onLoginLoad() in der nächsten Zeile muss wegen IE sein
            // da der das tlw. direkt im cache hat und das frame.onLoad nicht mitkriegt
            this.onLoginLoad();
            Ext.EventManager.on(frame, 'load', this.onLoginLoad, this);
        }, this, { delay: 1 });

        Kwf.User.Login.Dialog.superclass.initComponent.call(this);
    },

    _getDoc: function() {
        var frame = this.loginPanel.body.first('iframe');
        if(Ext.isIE){
            return frame.dom.contentWindow.document;
        } else {
            return (frame.dom.contentDocument || window.frames[id].document);
        }
    },

    onLoginLoad : function() {
        var doc = this._getDoc();

        if(doc && doc.body){
            if (doc.body.innerHTML.match(/successful/)) {
                var sessionToken = doc.body.innerHTML.match(/sessionToken:([^:]+):/);
                if (sessionToken[1]) {
                    Kwf.sessionToken = sessionToken[1];
                }
                this.hide();
                if (this.location) {
                    location.href = this.location;
                } else {
                    if (Kwf.menu) Kwf.menu.reload();
                    if (this.success) {
                        Ext.callback(this.success, this.scope);
                    }
                }
            } else if (doc.getElementsByName('username').length >= 1) {
                if (doc.activeElement && doc.activeElement.tagName.toLowerCase() != 'input') { //only focus() if not password has focus (to avoid users typing their password into username)
                    doc.getElementsByName('username')[0].focus();
                }
            }
        }
    },

    lostPassword: function() {
        Ext.Msg.prompt(trlKwf('Password lost'), trlKwf('Please enter your email address'), function(btn, email) {
            if (btn == 'ok') {
                var lostPasswordResultDialog = function(response, options, result) {
                    Ext.Msg.show({
                        title: 'Lost password',
                        msg: result.message,
                        width: 270,
                        buttons: Ext.MessageBox.OK
                    }, this);
                };
                Ext.Ajax.request({
                    mask: true,
                    url: '/kwf/user/login/json-lost-password',
                    params: { email: email },
                    success: lostPasswordResultDialog
                });
            }
        }, this);
    },

    showLogin: function() {
        this.show();
    }
});

