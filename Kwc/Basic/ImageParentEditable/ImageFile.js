Ext2.ns('Kwc.Basic.ImageParentEditable');
Kwc.Basic.ImageParentEditable.ImageFile = Ext2.extend(Kwc.Abstract.Image.ImageFile, {
    _showParentImage: null,
    _parentImageValue: null,
    initComponent: function() {
        Kwc.Basic.ImageParentEditable.ImageFile.superclass.initComponent.call(this);
    },
    setValue: function(v)
    {
        if (v && v.uploadId) {
            Kwc.Basic.ImageParentEditable.ImageFile.superclass.setValue.call(this, v);
            this.deleteButton.show();
            this._showParentImage = false;
        } else {
            Kwc.Basic.ImageParentEditable.ImageFile.superclass.setValue.call(this, this._parentImageValue);
            this.deleteButton.hide();
            this._showParentImage = true;
        }
    },
    getValue: function()
    {
        if (this._showParentImage) {
            return null;
        } else {
            return Kwc.Basic.ImageParentEditable.ImageFile.superclass.getValue.call(this);
        }
    },
    setParentImageValue: function(v)
    {
        this._parentImageValue = v;
        if (this._showParentImage) {
            Kwc.Basic.ImageParentEditable.ImageFile.superclass.setValue.call(this, this._parentImageValue);
        }
    }
});

Ext2.reg('kwc.imageparenteditable.imagefile', Kwc.Basic.ImageParentEditable.ImageFile);
