Kwc.Abstract.Image.ImageFile = Ext2.extend(Kwf.Form.File, {

    _completeValue: null,

    initComponent: function() {
        Kwc.Abstract.Image.ImageFile.superclass.initComponent.call(this);
        this.on('uploaded', function(field, value) {
            if (value) {
                var fs = this.ownerCt.ownerCt.items.find(function(i){return i.xtype=='fieldset'});
                if (fs) {
                    fs.find('autoFillWithFilename', 'filename').forEach(function (f) {
                        var v = value.uploaded_filename || value.filename;
                        v = v.toLowerCase().replace(/ä/g, 'ae').replace(/ö/g, 'oe')
                            .replace(/ü/g, 'ue').replace(/ß/g, 'ss')
                            .replace(/[^a-z0-9]/g, '-').replace(/\-\-+/g, '-');
                        f.setValue(v);
                    }, this);
                }
            }
        }, this);
    },

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

Ext2.reg('kwc.imagefile', Kwc.Abstract.Image.ImageFile);
