Kwc.Abstract.Image.ImageFile = Ext.extend(Kwf.Form.File, {
    contentWidth: null,

    afterRender: function() {
        Kwc.Abstract.Image.ImageFile.superclass.afterRender.call(this);
        this.deleteButton.setText(trlKwf('delete'));
    },

    setValue: function (value) {
        this.contentWidth = value.contentWidth;
        Kwc.Abstract.Image.ImageFile.superclass.setValue.call(this, value);
    }
});

Ext.reg('kwc.imagefile', Kwc.Abstract.Image.ImageFile);
