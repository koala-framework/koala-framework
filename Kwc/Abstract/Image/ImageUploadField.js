Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.ImageUploadField = Ext.extend(Ext.Panel, {

    baseParams: {},

    initComponent: function() {
        Kwc.Abstract.Image.ImageUploadField.superclass.initComponent.call(this);
        var dimensionsField = this._getDimensionsField();
        if (dimensionsField) {// because it's possible to define only a single dimension
            dimensionsField.on('change', function (dimension) {
                this._setPreviewUrl(dimension);
            }, this);
        }
        var fileUploadField = this._getFileUploadField();
        fileUploadField.on('change', function (el, value) {
            var dimensionsField = this._getDimensionsField();
            var dimension = null;
            if (dimensionsField) {
                dimension = dimensionsField.getValue();
            }
            this._setPreviewUrl(dimension);
        }, this);
    },

    _getFileUploadField: function () {
        return this.findByType('kwf.file')[0];
    },

    _getDimensionsField: function () {
        return this.findByType('kwc.image.dimensionfield')[0];
    },

    _setPreviewUrl: function(dimension) {
        var cropParams = '';
        if (dimension && dimension.cropData) {
            cropParams += dimension.cropData.x ? '&cropX='+dimension.cropData.x : '';
            cropParams += dimension.cropData.y ? '&cropY='+dimension.cropData.y : '';
            cropParams += dimension.cropData.width ? '&cropWidth='+dimension.cropData.width : '';
            cropParams += dimension.cropData.height ? '&cropHeight='+dimension.cropData.height : '';
        }
        var link = this.previewUrl+'?componentId='+this.baseParams.componentId+cropParams+'&';
        var fileUploadField = this._getFileUploadField();
        fileUploadField.setPreviewUrl(link);
    },

    setFormBaseParams: function(params) {
        Ext.apply(this.baseParams, params);
    }
});

Ext.reg('kwc.image.imageuploadfield', Kwc.Abstract.Image.ImageUploadField);
