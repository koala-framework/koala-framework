Ext.namespace('Vps.User.Activate');
Vps.User.Activate.Dialog = Ext.extend(Ext.Window,
{
    initComponent: function()
    {
        this.height = 160;
        this.width = 310;
        this.modal = true;
        this.title = 'Account activation';
        this.resizable = false;
        this.closable = true;
        Vps.User.Activate.Dialog.superclass.initComponent.call(this);
    },
    afterRender : function()
    {
        // Form & Login
        Vps.User.Login.Dialog.superclass.afterRender.call(this);
        var frameHtml = '<iframe scrolling="no" src="/login/showForm" width="100%" '+
                        'height="100%" style="border: 0px"></iframe>';
        var frame = Ext.DomHelper.append(this.body, frameHtml, true);

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
                                url: '/login/jsonLostPassword',
                                params: { email: email },
                                success: lostPasswordResultDialog
                            });
                        }
                    }, this);
                }, this);
            }
        }
        Ext.EventManager.on(frame, 'load', cb, this);

    },
    showLogin: function() {
        this.show();
    }
});