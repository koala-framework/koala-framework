Ext2.namespace('Kwc.Basic.ImageParentEditable');
Kwc.Basic.ImageParentEditable.ImageUploadField = Ext2.extend(Kwf.Form.Field.Image.UploadField, {
    initComponent: function() {
        Kwc.Basic.ImageParentEditable.ImageUploadField.superclass.initComponent.call(this);
    },
    afterRender: function() {
        Kwc.Basic.ImageParentEditable.ImageUploadField.superclass.afterRender.call(this);
        var container = this.ownerCt;
        while (container) {
            var imageFile = container.findBy(function(i) {
                return i instanceof Kwf.Form.Field.Image.ImageFile
                    && !(i.ownerCt instanceof Kwc.Basic.ImageParentEditable.ImageUploadField);
            }, this);
            if (imageFile.length) break;
            container = container.ownerCt;
        }
        imageFile = imageFile[0];
        imageFile.on('change', function(el, value) {
            this.findByType('kwc.imageparenteditable.imagefile')[0].setParentImageValue(value);
        }, this);
    }
});
Ext2.reg('kwc.imageparenteditable.imageuploadfield', Kwc.Basic.ImageParentEditable.ImageUploadField);
