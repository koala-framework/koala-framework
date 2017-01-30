Ext2.namespace('Kwf.Form.Field.Image');
Kwf.Form.Field.Image.UploadField = Ext2.extend(Ext2.Panel, {

    _scaleFactor: null,
    baseParams: null,

    initComponent: function() {
        this.baseParams = {};
        Kwf.Form.Field.Image.UploadField.superclass.initComponent.call(this);
        var dimensionField = this._getDimensionField();
        if (dimensionField) {// because it's possible to define only a single dimension
            dimensionField.on('render', function () {
                // fileUploadField also has to be rendered
                var dimensionField = this._getDimensionField();
                var fileUploadField = this._getFileUploadField();
                fileUploadField.container.addClass('kwf-form-field-image-uploadfield-container');
                if (dimensionField.getEl() && fileUploadField.getEl()) {
                    dimensionField.getEl().parent().parent().addClass('kwf-form-field-image-dimensionfield-container');
                }
            }, this);
            dimensionField.on('change', function (dimension) {
                this._setPreviewUrl(dimension);
                this._checkForImageTooSmall();
            }, this);
        }
        var fileUploadField = this._getFileUploadField();
        fileUploadField.on('change', function (el, value) {
            var dimensionField = this._getDimensionField();
            if (dimensionField && value.contentWidth) {
                dimensionField.setContentWidth(value.contentWidth);
            }
            if (!value || !value.mimeType || !value.mimeType.match(/(^image\/)/)) {
                dimensionField.newImageUploaded('');
                this._getFileUploadField().setPreviewUrl(null);
                this.removeClass('image-uploaded');
                return;
            }
            this.addClass('image-uploaded');
            var dimensionValue = null;
            if (dimensionField) {
                this._scaleFactor = value.imageHandyScaleFactor;
                dimensionField.setScaleFactor(value.imageHandyScaleFactor);
                dimensionField.newImageUploaded(value);
                dimensionValue = dimensionField.getValue();
                if (dimensionValue.dimension) {
                    this._checkForImageTooSmall();
                }
            }
            this._setPreviewUrl(dimensionValue);
        }, this);
    },

    _checkForImageTooSmall: function () {
        var dimensionField = this._getDimensionField();
        var value = Kwf.clone(dimensionField.getValue());
        var fileUploadField = this._getFileUploadField();
        if (!fileUploadField.getEl().child('.hover-background')) return;
        var scaleFactor = this._scaleFactor;
        this._validateImageTooSmallUserNotification(value, dimensionField.resolvedDimensions, scaleFactor, fileUploadField, dimensionField, dimensionField.dpr2Check);
    },

    _validateImageTooSmallUserNotification: function (value, dimensions, scaleFactor, fileUploadField, dimensionField, dpr2) {
        if (!fileUploadField.getEl().child('.hover-background .message')) {
            fileUploadField.getEl().child('.hover-background').createChild({
                html:trlKwf('Caution! Image size of uploaded image does not match minimum requirement.'),
                cls: 'message'
            });
        } else {
            fileUploadField.getEl().child('.hover-background .message')
                .update(trlKwf('Caution! Image size of uploaded image does not match minimum requirement.'));
        }
        if (!value.cropData) {
            var dimension = dimensionField.dimensions[value.dimension];
            value.cropData = Kwf.Form.Field.Image.CropImage
                .calculateDefaultCrop(dimension.width, dimension.height,
                    dimensionField.imageData.imageWidth, dimensionField.imageData.imageHeight);
        } else {
            fileUploadField.getEl().child('.hover-background .message')
                .update(trlKwf('Caution! Crop region does not match minimum requirement.'));
        }
        if (!Kwf.Form.Field.Image.DimensionField.isValidImageSize(value, dimensions, dpr2)) {
            this.getEl().addClass('error');
            fileUploadField.getEl().child('.hover-background').addClass('error');
        } else {
            this.getEl().removeClass('error');
            fileUploadField.getEl().child('.hover-background').removeClass('error');
        }
    },

    _getFileUploadField: function () {
        return this.findBy(function(i) {
            return i instanceof Kwf.Form.Field.Image.ImageFile;
        }, this)[0];
    },

    _getDimensionField: function () {
        return this.findByType('kwf.form.field.image.dimensionfield')[0];
    },

    _setPreviewUrl: function(value) {
        // This function changes value. This is bad behaviour!
        var previewParams = {
            componentId: this.baseParams.componentId
        };
        if (value) {
            if (value.dimension != null) {
                previewParams.dimension = value.dimension;
                var dimension = this._getDimensionField().resolvedDimensions[value.dimension];
                var outWidth = (dimension.width == 'user' || dimension.aspectRatio) ? value.width : dimension.width;
                var outHeight = (dimension.height == 'user' || dimension.aspectRatio) ? value.height : dimension.height;
                if (dimension && value.cropData) {
                    if (outWidth != 0 && outHeight != 0 && dimension.cover
                        && Math.floor(outWidth * 100 / outHeight)
                            != Math.floor(value.cropData.width * 100 / value.cropData.height)
                    ) {
                        var result = Kwf.Form.Field.Image.CropImage
                            .calculateDefaultCrop(outWidth, outHeight,
                                                value.cropData.width, value.cropData.height);
                        // This also resets the value of dimensionField. Thisway also
                        // dimensionWindow and cropImage component access correct values.
                        // Also if only "save" is clicked and nothing was changed
                        // the corrected values are saved.
                        value.cropData.width = result.width;
                        value.cropData.height = result.height;
                        value.cropData.x += result.x;
                        value.cropData.y += result.y;
                    }
                } else if (dimension) {
                    previewParams.dimension_width = dimension.width;
                    previewParams.dimension_height = dimension.height;
                    previewParams.dimension_cover = dimension.cover;
                }
            }

            if (value.width != null) previewParams.width = value.width;
            if (value.height != null) previewParams.height = value.height;

            if (value.cropData) {
                if (value.cropData.x != null) previewParams.cropX = value.cropData.x;
                if (value.cropData.y != null) previewParams.cropY = value.cropData.y;
                if (value.cropData.width != null) previewParams.cropWidth = value.cropData.width;
                if (value.cropData.height != null) previewParams.cropHeight = value.cropData.height;
            }
        }

        this._getFileUploadField().setPreviewUrl(this.previewUrl+'?'
            +Ext2.urlEncode(previewParams)+'&'
        );
    },

    setFormBaseParams: function(params) {
        Ext2.apply(this.baseParams, params);
    }
});

Ext2.reg('kwf.form.field.image.uploadfield', Kwf.Form.Field.Image.UploadField);
