Ext.namespace('Vps.User.Login');
Vps.User.Login.Dialog = Ext.extend(Ext.Window,
{
    initComponent: function()
    {
        this.height = 200;
        this.width = 310;
        this.modal = true;
        this.title = 'Login';
        this.resizable = false;
        this.closable = true;
        this.layout = 'border';
        this.loginPanel = new Ext.Panel({
            baseCls: 'x-plain',
            region: 'center',
            border: false,
            html: '<iframe scrolling="no" src="/vps/login/showForm" width="100%" '+
                    'height="100%" style="border: 0px"></iframe>'
        });
        this.items = [{
            baseCls: 'x-plain',
            cls: 'vps-login-header',
            region: 'north',
            height: 50,
            autoLoad: '/vps/login/header',
            border: false
        },this.loginPanel];

        this.loginPanel.on('render', function(panel) {
            var frame = this.loginPanel.body.first('iframe');
            Ext.EventManager.on(frame, 'load', this.onLoginLoad, this);
        }, this, { delay: 1 });

        Vps.User.Login.Dialog.superclass.initComponent.call(this);
    },

    onLoginLoad : function() {
        var frame = this.loginPanel.body.first('iframe');
        if(Ext.isIE){
            doc = frame.dom.contentWindow.document;
        } else {
            doc = (frame.dom.contentDocument || window.frames[id].document);
        }
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
        }
    },
    showLogin: function() {
        this.show();
    }
});