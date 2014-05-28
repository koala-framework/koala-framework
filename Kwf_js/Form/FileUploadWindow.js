Kwf.Form.FileUploadWindow = Ext2.extend(Ext2.Window, {
    title: trlKwf('File upload'),
    closeAction: 'close',
    modal: true,
    width: 350,
    height: 120,
    initComponent: function() {
        this.addEvents(['uploaded']);
        if (!this.maxResolution) {
            this.maxResolution = 0;
        }
        this.form = new Ext2.FormPanel({
            baseCls: 'x2-plain',
            style: 'padding: 10px;',
            url: '/kwf/media/upload/json-upload'+'?maxResolution='+this.maxResolution,
            fileUpload: true,
            items: [{
                name: 'Filedata',
                xtype: 'textfield',
                inputType: 'file',
                hideLabel: true
            }]
        });
        this.items = this.form;
        this.buttons = [{
            text: trlKwf('OK'),
            handler: function() {
                this.form.getForm().submit({
                    success: function(form, action) {
                        this.fireEvent('uploaded', this, action.result);
                        this.close();
                    },
                    scope: this
                });
            },
            scope: this
        },{
            text: trlKwf('Cancel'),
            handler: function() {
                this.close();
            },
            scope: this
        }];
        Kwf.Form.FileUploadWindow.superclass.initComponent.call(this);
    }
});
