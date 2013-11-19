Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.ImageUploadField = Ext.extend(Ext.Panel, {

    baseParams: null,
    _dimensionFieldRendered: false,
    _fileUploadFieldRendered: false,

    initComponent: function() {
        this.baseParams = {};
        Kwc.Abstract.Image.ImageUploadField.superclass.initComponent.call(this);
        var dimensionField = this._getDimensionField();
        if (dimensionField) {// because it's possible to define only a single dimension
            dimensionField.on('render', function () {
                this._dimensionFieldRendered = true;
                this._alignDimensionField();
            }, this);
            dimensionField.on('change', function (dimension) {
                this._setPreviewUrl(dimension);
            }, this);
        }
        var fileUploadField = this._getFileUploadField();
        fileUploadField.on('change', function (el, value) {
            var dimensionField = this._getDimensionField();
            if (!value || !value.mimeType.match(/(^image\/)/)) {
                dimensionField.newImageUploaded('');
                this._getFileUploadField().setPreviewUrl(null);
                return;
            }
            var dimension = null;
            if (dimensionField) {
                dimensionField.newImageUploaded(value);
                dimension = dimensionField.getValue();
            }
            this._setPreviewUrl(dimension);
        }, this);
        fileUploadField.on('render', function () {
            this._fileUploadFieldRendered = true;
            this._alignDimensionField();
        }, this);
    },

    _alignDimensionField: function () {
        if (this._dimensionFieldRendered && this._fileUploadFieldRendered) {
            var dimensionField = this._getDimensionField();
            var fileUploadField = this._getFileUploadField();
            dimensionField.getEl().parent().parent().addClass('kwc-dimensionfield-container');
            dimensionField.getEl().anchorTo(fileUploadField.getEl().child('.box'), 'br', [10, -42]);
        }
    },

    _getFileUploadField: function () {
        return this.findByType('kwf.file')[0];
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
