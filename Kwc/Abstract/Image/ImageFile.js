Kwc.Abstract.Image.ImageFile = Ext.extend(Kwf.Form.File, {

    afterRender: function() {
        Kwc.Abstract.Image.ImageFile.superclass.afterRender.call(this);
        this.deleteButton.setText(trlKwf('delete'));
        this.uploadButton.setText(trlKwf('Upload Image'));
    },

    setValue: function (value) {
        if (value) {
            this.uploadButton.setText(trlKwf('Change Image'));
        } else {
            this.uploadButton.setText(trlKwf('Upload Image'));
        }
        Kwc.Abstract.Image.ImageFile.superclass.setValue.call(this, value);
    }
});

Ext.reg('kwc.imagefile', Kwc.Abstract.Image.ImageFile);
