Kwc.Abstract.Image.ImageFile = Ext.extend(Kwf.Form.File, {

    _completeValue: null,

    afterRender: function() {
        Kwc.Abstract.Image.ImageFile.superclass.afterRender.call(this);
        this.deleteButton.setText(trlKwf('delete'));
        this.uploadButton.setText(trlKwf('Upload Image'));
    },

    getImageWidth: function () {
        return this._completeValue.imageWidth;
    },

    getImageHeight: function () {
        return this._completeValue.imageHeight;
    },

    setValue: function (value) {
        this._completeValue = value;
        if (value.uploadId) {
            this.uploadButton.setText(trlKwf('Change Image'));
        } else {
            this.uploadButton.setText(trlKwf('Upload Image'));
        }
        Kwc.Abstract.Image.ImageFile.superclass.setValue.call(this, value);
    }
});

Ext.reg('kwc.imagefile', Kwc.Abstract.Image.ImageFile);
