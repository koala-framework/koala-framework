Ext2.namespace('Kwc.Basic.ImageEnlarge');
Kwc.Basic.ImageEnlarge.ImageUploadField = Ext2.extend(Kwc.Abstract.Image.ImageUploadField, {

    _findUseCropCheckbox: function () {
        var useCropCheckboxes = this.findParentBy(function (component, container){
            if (component.identifier == 'kwc-basic-imageenlarge-form') {
                return true;
            }
            return false;
        }, this).findBy(function (component, container) {
            if (component.xtype == 'checkbox' && component.getEl().hasClass('kwc-basic-imageenlarge-enlargetag-checkbox-usecrop')) {
                return true;
            }
            return false;
        }, this);
        return useCropCheckboxes[0];
    },

    _isValidateImageTooSmallUsingImageEnlargeDimensions: function () {
        // In this component there is no option to select a different sub-component
        return true;
    },

    _validateImageTooSmallUserNotification: function (value, dimensions, scaleFactor, fileUploadField, dimensionField, dpr2) {
        var valueCopy = Kwf.clone(value);
        if (this._isValidateImageTooSmallUsingImageEnlargeDimensions()) { // check if previewimage is selected
            var cropCheckbox = this._findUseCropCheckbox();
            if (!cropCheckbox.kwcBasicImageEnlargeHasListener) {
                // Can't be done in afterRender because cropCheckbox isn't rendered
                // at this moment. There is also no later point to do this.
                cropCheckbox.on('check', function () {
                    this._checkForImageTooSmall();
                }, this);
                cropCheckbox.kwcBasicImageEnlargeHasListener = true;
            }
            if (!cropCheckbox.checked && valueCopy.cropData) {// use_crop_region isn't selected
                valueCopy.cropData.width = fileUploadField.getImageWidth();
                valueCopy.cropData.height = fileUploadField.getImageHeight();
            }

            // change min-requirement text
            var pixelString = Kwc.Abstract.Image.DimensionField
                .getDimensionPixelString(this.imageEnlargeDimension, value, dpr2);
            dimensionField.getEl().child('.kwc-abstract-image-dimension-name')
                .update(trlKwf('At least: ')+pixelString);

            // check size and show message at fileUploadField
            valueCopy.dimension = 0;
            enlargeDimensions = new Array();
            enlargeDimensions.add(this.imageEnlargeDimension);
            if (!Kwc.Abstract.Image.DimensionField
                    .isValidImageSize(valueCopy, enlargeDimensions, dpr2)) {
                this.getEl().addClass('error');
                fileUploadField.getEl().child('.hover-background').addClass('error');
                if (!fileUploadField.getEl().child('.hover-background .message')) {
                    fileUploadField.getEl().child('.hover-background').createChild({
                        html:trlKwf('Caution! Image size of uploaded image does not match minimum requirement for enlarge image.'),
                        cls: 'message'
                    });
                } else {
                    fileUploadField.getEl().child('.hover-background .message')
                        .update(trlKwf('Caution! Image size of uploaded image does not match minimum requirement for enlarge image.'));
                }
                if (cropCheckbox.checked) {
                    fileUploadField.getEl().child('.hover-background .message')
                        .update(trlKwf('Caution! Crop region does not match minimum requirement for enlarge image.'));
                }
            } else {
                var pixelString = Kwc.Abstract.Image.DimensionField
                    .getDimensionPixelString(dimensions[value.dimension], value, dpr2);
                dimensionField.getEl().child('.kwc-abstract-image-dimension-name')
                    .update(trlKwf('At least: ')+pixelString);

                Kwc.Basic.ImageEnlarge.ImageUploadField.superclass
                    ._validateImageTooSmallUserNotification.call(this, value, dimensions, scaleFactor, fileUploadField, dimensionField, dpr2);
            }
        } else {
            var pixelString = Kwc.Abstract.Image.DimensionField
                .getDimensionPixelString(dimensions[value.dimension], value, dpr2);
            dimensionField.getEl().child('.kwc-abstract-image-dimension-name')
                .update(trlKwf('At least: ')+pixelString);
            Kwc.Basic.ImageEnlarge.ImageUploadField.superclass
                ._validateImageTooSmallUserNotification.call(this, value, dimensions, scaleFactor, fileUploadField, dimensionField, dpr2);
        }
    }
});

Ext2.reg('kwc.basic.imageenlarge.imageuploadfield', Kwc.Basic.ImageEnlarge.ImageUploadField);
