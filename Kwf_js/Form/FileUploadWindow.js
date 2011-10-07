Vps.Form.FileUploadWindow = Ext.extend(Ext.Window, {
    title: trlVps('File upload'),
    closeAction: 'close',
    modal: true,
    width: 350,
    height: 120,
    initComponent: function() {
        this.addEvents(['uploaded']);
        if (!this.maxResolution) {
            this.maxResolution = 0;
        }
        this.form = new Ext.FormPanel({
            baseCls: 'x-plain',
            style: 'padding: 10px;',
            url: '/vps/media/upload/json-upload'+'?maxResolution='+this.maxResolution,
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
            text: trlVps('OK'),
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
            text: trlVps('Cancel'),
            handler: function() {
                this.close();
            },
            scope: this
        }];
        Vps.Form.FileUploadWindow.superclass.initComponent.call(this);
    }
});
