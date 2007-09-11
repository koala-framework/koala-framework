Ext.namespace('Vps.User', 'Vps.User.Login');
Vps.User.Login.Dialog = function(renderTo, config)
{
    Ext.apply(this, config);
    renderTo = renderTo || Ext.get(document.body).createChild();

    this.dialog = new Ext.BasicDialog(renderTo, {
        height: 160,
        width: 310,
        modal: true,
        proxyDrag: true,
        shadow: true,
        title: 'Login',
        closable: false,
        collapsible: false,
        resizable: false
    });
    
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
