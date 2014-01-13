Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.ImageUploadField = Ext.extend(Ext.Panel, {

    _scaleFactor: null,
    baseParams: null,

    initComponent: function() {
        this.baseParams = {};
        Kwc.Abstract.Image.ImageUploadField.superclass.initComponent.call(this);
        var dimensionField = this._getDimensionField();
        if (dimensionField) {// because it's possible to define only a single dimension
            dimensionField.on('render', function () {
                // fileUploadField also has to be rendered
                var dimensionField = this._getDimensionField();
                var fileUploadField = this._getFileUploadField();
                fileUploadField.container.addClass('kwc-abstract-image-imageuploadfile-container');
                if (dimensionField.getEl() && fileUploadField.getEl()) {
                    dimensionField.getEl().parent().parent().addClass('kwc-dimensionfield-container');
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
        if (dimensionField.dpr2Check) scaleFactor /= 2;
        this._validateImageTooSmallUserNotification(value, dimensionField.resolvedDimensions, scaleFactor, fileUploadField, dimensionField);
    },

    _validateImageTooSmallUserNotification: function (value, dimensions, scaleFactor, fileUploadField, dimensionField) {
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
            value.cropData = Kwc.Abstract.Image.CropImage
                .calculateDefaultCrop(dimension.width, dimension.height,
                    dimensionField.imageData.imageWidth, dimensionField.imageData.imageHeight);
        } else {
            fileUploadField.getEl().child('.hover-background .message')
                .update(trlKwf('Caution! Crop region does not match minimum requirement.'));
        }
        if (!Kwc.Abstract.Image.DimensionField.isValidImageSize(value, dimensions, scaleFactor)) {
            this.getEl().addClass('error');
            fileUploadField.getEl().child('.hover-background').addClass('error');
        } else {
            this.getEl().removeClass('error');
            fileUploadField.getEl().child('.hover-background').removeClass('error');
        }
    },

    _getFileUploadField: function () {
        return this.findByType('kwc.imagefile')[0];
    },

    _getDimensionField: function () {
        return this.findByType('kwc.image.dimensionfield')[0];
    },

    _setPreviewUrl: function(dimension) {
        var previewParams = {
            componentId: this.baseParams.componentId
        };
        if (dimension && dimension.dimension != null) previewParams.dimension = dimension.dimension;

        value = this._getDimensionField().getValue();
        if (value.width != null) previewParams.width = value.width;
        if (value.height != null) previewParams.height = value.height;

        if (dimension && dimension.cropData) {
            if (dimension.cropData.x != null) previewParams.cropX = dimension.cropData.x;
            if (dimension.cropData.y != null) previewParams.cropY = dimension.cropData.y;
            if (dimension.cropData.width != null) previewParams.cropWidth = dimension.cropData.width;
            if (dimension.cropData.height != null) previewParams.cropHeight = dimension.cropData.height;
        }
        this._getFileUploadField().setPreviewUrl(this.previewUrl+'?'
            +Ext.urlEncode(previewParams)+'&'
        );
    },

    setFormBaseParams: function(params) {
        Ext.apply(this.baseParams, params);
    }
});

Ext.reg('kwc.image.imageuploadfield', Kwc.Abstract.Image.ImageUploadField);
