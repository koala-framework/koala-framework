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
                this._alignDimensionField();
            }, this);
            dimensionField.on('change', function (dimension) {
                this._setPreviewUrl(dimension);
                this._checkForImageTooSmall(dimension);
            }, this);
        }
        var fileUploadField = this._getFileUploadField();
        fileUploadField.on('change', function (el, value) {
            var dimensionField = this._getDimensionField();
            if (!value || !value.mimeType || !value.mimeType.match(/(^image\/)/)) {
                dimensionField.newImageUploaded('');
                this._getFileUploadField().setPreviewUrl(null);
                this.removeClass('image-uploaded');
                return;
            }
            this.addClass('image-uploaded');
            var dimension = null;
            if (dimensionField) {
//                dimensionField.setContentWidth(value.contentWidth);
                this._scaleFactor = value.imageHandyScaleFactor;
                dimensionField.setScaleFactor(value.imageHandyScaleFactor);
                dimensionField.newImageUploaded(value);
                dimension = dimensionField.getValue();
            }
            this._setPreviewUrl(dimension);
        }, this);
    },

    _checkForImageTooSmall: function (value) {
        var dimensionField = this._getDimensionField();
        var fileUploadField = this._getFileUploadField();
        if (!fileUploadField.getEl().child('.hover-background')) return;
        if (!Kwc.Abstract.Image.DimensionField.checkImageSize(value, dimensionField.dimensions, this._scaleFactor)) {
            fileUploadField.getEl().child('.hover-background').addClass('error');
            if (!fileUploadField.getEl().child('.hover-background .message')) {
                fileUploadField.getEl().child('.hover-background').createChild({
                    html:trlKwf('CAUTION! Image size does not match minimum requirement.'),
                    cls: 'message'
                });
            }
        } else {
            fileUploadField.getEl().child('.hover-background').removeClass('error');
        }
    },

    _alignDimensionField: function () {
        var dimensionField = this._getDimensionField();
        var fileUploadField = this._getFileUploadField();
        fileUploadField.container.addClass('kwc-abstract-image-imageuploadfile-container');
        if (dimensionField.getEl() && fileUploadField.getEl()) {
            dimensionField.getEl().parent().parent().addClass('kwc-dimensionfield-container');
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
