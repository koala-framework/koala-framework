Ext.namespace('Vps.User.Login');
Vps.User.Login.Dialog = Ext.extend(Ext.Window,
{
    initComponent: function() {
        this.height = 160;
        this.width = 310;
        this.modal = true;
        this.proxyDrag = true;
        this.title = 'Login';
        this.resizable = false;
        Vps.User.Login.Dialog.superclass.initComponent.call(this);

        this.render(Ext.getBody());

        Ext.DomHelper.append(this.body, '<iframe id="loginframe" scrolling="no" src="/login/showForm" width="100%" height="100%" style="border: 0px"></iframe>');
        var frame = Ext.get('loginframe');
        function cb(){
            if(Ext.isIE){
                doc = frame.dom.contentWindow.document;
            }else {
                doc = (frame.dom.contentDocument || window.frames[id].document);
            }
            if(doc && doc.body){
                if (doc.body.innerHTML.match(/successful/)) {
                    this.hide();
                    if (this.location) {
                        location.href = this.location;
                    } else {
                        if(Vps.menu) Vps.menu.reload();
                        if(this.success) {
                            Ext.callback(this.success, this.scope);
                        }
                    }
                }
            }
        }
        Ext.EventManager.on(frame, 'load', cb, this);
    },
    showLogin: function() {
        this.show();
    }
});