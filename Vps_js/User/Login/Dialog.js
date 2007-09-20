Ext.namespace('Vps.User.Login');
Vps.User.Login.Dialog = function(config)
{
    this.dialog = new Ext.Window({
        height: 160,
        width: 310,
        modal: false,
        proxyDrag: true,
        shadow: true,
        title: 'Login',
        closable: true,
        collapsible: false,
        resizable: false
    });
    this.dialog.render(Ext.getBody());

    Ext.DomHelper.append(this.dialog.body, '<iframe id="loginframe" scrolling="no" src="/login/showForm" width="100%" height="100%" style="border: 0px"></iframe>');
    var frame = Ext.get('loginframe');

    function cb(){
        if(Ext.isIE){
            doc = frame.dom.contentWindow.document;
        }else {
            doc = (frame.dom.contentDocument || window.frames[id].document);
        }
        if(doc && doc.body){
            if (doc.body.innerHTML.match(/successful/)) {
                this.dialog.hide();
                if(Vps.menu) Vps.menu.reload();
                if(this.success) {
                    Ext.callback(this.success, this.scope);
                }
            }
        }
    }

    Ext.EventManager.on(frame, 'load', cb, this);

    Vps.User.Login.Dialog.superclass.constructor.call(this, config);
};


Ext.extend(Vps.User.Login.Dialog, Ext.util.Observable,
{
    show: function() {
        this.dialog.show();
    },
    showLogin: function() {
        this.dialog.show();
    }
});
