Ext2.namespace('Kwf.User.Activate');
Kwf.User.Activate.Dialog = Ext2.extend(Ext2.Window,
{
    initComponent: function()
    {
        this.height = 160;
        this.width = 310;
        this.modal = true;
        this.title = trlKwf('Account activation');
        this.resizable = false;
        this.closable = true;
        Kwf.User.Activate.Dialog.superclass.initComponent.call(this);
    },
    afterRender : function()
    {
        // Form & Login
        Kwf.User.Login.Dialog.superclass.afterRender.call(this);
        var frameHtml = '<iframe scrolling="no" src="/login/show-form" width="100%" '+
                        'height="100%" style="border: 0px"></iframe>';
        var frame = Ext2.DomHelper.append(this.body, frameHtml, true);

        function cb(){
            if(Ext2.isIE){
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
                        if(Kwf.menu) Kwf.menu.reload();
                        if(this.success) {
                            Ext2.callback(this.success, this.scope);
                        }
                    }
                } else {
                    doc.getElementsByName('username')[0].focus();
                }

                Ext2.EventManager.on(doc.getElementById('lostPassword'), 'click', function() {
                    Ext2.Msg.prompt(trlKwf('Password lost'), trlKwf('Please enter your email address'), function(btn, email) {
                        if (btn == 'ok') {
                            var lostPasswordResultDialog = function(response, options, result) {
                                Ext2.Msg.show({
                                    title: 'Lost password',
                                    msg: result.message,
                                    width: 270,
                                    buttons: Ext2.MessageBox.OK
                                }, this);
                            };
                            Ext2.Ajax.request({
                                url: '/login/json-lost-password',
                                params: { email: email },
                                success: lostPasswordResultDialog
                            });
                        }
                    }, this);
                }, this);
            }
        }
        Ext2.EventManager.on(frame, 'load', cb, this);

    },
    showLogin: function() {
        this.show();
    }
});