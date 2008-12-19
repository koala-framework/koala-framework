Ext.namespace('Vps.User.Login');
Vps.User.Login.Dialog = Ext.extend(Ext.Window,
{
    initComponent: function()
    {
        this.height = 240;
        this.width = 310;
        this.modal = true;
        this.title = 'Login';
        this.resizable = false;
        this.closable = true;
        this.layout = 'border';
        this.loginPanel = new Ext.Panel({
            baseCls: 'x-plain',
            region: 'south',
            border: false,
            height: 125,
            html: '<iframe scrolling="no" src="/vps/user/login/show-form" width="100%" '+
                    'height="100%" style="border: 0px"></iframe>'
        });
        this.items = [{
            baseCls: 'x-plain',
            cls: 'vps-login-header',
            region: 'north',
            height: 80,
            autoLoad: '/vps/user/login/header',
            border: false
        },{
            baseCls: 'x-plain',
            region: 'center',
            bodyStyle: 'padding: 10px;',
            html: this.message,
            border: false
        }, this.loginPanel];

        this.loginPanel.on('render', function(panel) {
            var frame = this.loginPanel.body.first('iframe');
            var doc = this._getDoc();
            if (doc && doc.body) {
                this.onLoginLoad();
            } else {
                Ext.EventManager.on(frame, 'load', this.onLoginLoad, this);
            }
        }, this, { delay: 1 });

        Vps.User.Login.Dialog.superclass.initComponent.call(this);
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
/*
        // IE sux sometimes :)
        if (!doc || !doc.body) {
            Vps.log('deferring');
            this.onLoginLoad.defer(100, this);
            return ;
        }
*/
        if(doc && doc.body){
            if (doc.body.innerHTML.match(/successful/)) {
                this.hide();
                if (this.location) {
                    location.href = this.location;
                } else {
                    if (Vps.menu) Vps.menu.reload();
                    if (this.success) {
                        Ext.callback(this.success, this.scope);
                    }
                }
            } else {
                doc.getElementsByName('username')[0].focus();
            }

            Ext.EventManager.on(doc.getElementById('lostPassword'), 'click', function() {
                Ext.Msg.prompt(trlVps('Password lost'), trlVps('Please enter your email address'), function(btn, email) {
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
                            url: '/vps/user/login/json-lost-password',
                            params: { email: email },
                            success: lostPasswordResultDialog
                        });
                    }
                }, this);
            }, this);
        }
    },

    showLogin: function() {
        this.show();
    }
});





