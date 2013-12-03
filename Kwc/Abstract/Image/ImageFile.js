Kwc.Abstract.Image.ImageFile = Ext.extend(Kwf.Form.File, {
    initComponent: function() {
        Kwc.Abstract.Image.ImageFile.superclass.initComponent.call(this);
    },
    afterRender: function() {
        Kwc.Abstract.Image.ImageFile.superclass.afterRender.call(this);
        this.deleteButton.setText(trlKwf('delete'));
    }
});

Ext.reg('kwc.imagefile', Kwc.Abstract.Image.ImageFile);
