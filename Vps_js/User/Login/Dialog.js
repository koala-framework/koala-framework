Ext.namespace('Vps.User.Login'
Vps.User.Login.Dialog = Ext.extend(Ext.Windo

    initComponent: function
   
        this.height = 16
        this.width = 31
        this.modal = tru
        this.title = 'Login
        this.resizable = fals
        this.closable = tru
        Vps.User.Login.Dialog.superclass.initComponent.call(this
    
    afterRender : function
   
        Vps.User.Login.Dialog.superclass.afterRender.call(this
        var frameHtml = '<iframe scrolling="no" src="/login/showForm" width="100%" 
                        'height="100%" style="border: 0px"></iframe>
        var frame = Ext.DomHelper.append(this.body, frameHtml, true
        function cb(
            if(Ext.isIE
                doc = frame.dom.contentWindow.documen
            }else
                doc = (frame.dom.contentDocument || window.frames[id].document
           
            if(doc && doc.body
                if (doc.body.innerHTML.match(/successful/))
                    this.hide(
                    if (this.location)
                        location.href = this.locatio
                    } else
                        if(Vps.menu) Vps.menu.reload(
                        if(this.success)
                            Ext.callback(this.success, this.scope
                       
                   
                } else
                    doc.getElementsByName('username')[0].focus(
               
           
       
        Ext.EventManager.on(frame, 'load', cb, this
    
    showLogin: function()
        this.show(
   
}