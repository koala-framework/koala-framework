Kwf.Form.FileUploadWindow = Ext2.extend(Ext2.Window, {
    title: trlKwf('File upload'),
    closeAction: 'close',
    modal: true,
    width: 330,
    height: 140,
    initComponent: function() {
        this.addEvents(['uploaded']);
        this.form = new Ext2.FormPanel({
            baseCls: 'x2-plain',
            style: 'padding: 10px;',
            items: [{
                name: 'Filedata',
                xtype: 'kwf.file',
                hideLabel: true
            }]
        });
        this.form.findByType('kwf.file')[0].on('change', function(el, value, oldValue) {
            this.uploadValue = value;
        }, this);

        this.items = this.form;
        this.buttons = [{
            text: trlKwf('OK'),
            handler: function() {
                if (this.uploadValue) {
                    this.fireEvent('uploaded', this, { value: this.uploadValue, success: true });
                }
                this.close();
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
