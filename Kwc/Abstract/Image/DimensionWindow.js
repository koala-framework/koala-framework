Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.DimensionWindow = Ext.extend(Ext.Window, {

    dimensions: null,
    value: null, //contains width, height, cropdata

    title: trlKwf('Configure'),
    closeAction: 'close',
    modal: true,
    minWidth: 418,
    minHeight: 250,
    width: 418,
    height: 250,
    layout: 'fit',
    resizable: false,

    initComponent: function() {
        var radios = [];
        for (var i in this.dimensions) {
            radios.push({
                inputValue: i,
                boxLabel: Kwc.Abstract.Image.DimensionField.getDimensionString(this.dimensions, {dimension: i}),
                name: 'dimension',
                listeners: {
                    check: this._enableDisableFields,
                    scope: this
                }
            });
        }
        this._dimensionField = new Kwf.Form.RadioGroup({
            columns: 1,
            hideLabel: true,
            vertical: false,
            items: radios
        });
        this._widthField = new Ext.form.NumberField({
            width: 45,
            enableKeyEvents: true,
            validateOnBlur: false,
            validationEvent: false,
            allowNegative: false
        });
        this._heightField = new Ext.form.NumberField({
            width: 45,
            enableKeyEvents: true,
            validateOnBlur: false,
            validationEvent: false,
            allowNegative: false
        });
        this._xField = new Ext.BoxComponent({
            autoEl: {
                html: 'x',
                style: 'line-height:20px;text-align:center;padding-left:2px;padding-right:2px;'
            }
        });
        this._pxField = new Ext.BoxComponent({
            autoEl: {
                html: 'px',
                style: 'line-height:20px;text-align:center;padding-left:2px;'
            }
        });
        this._userSelection = new Ext.Panel({
            xtype: 'panel',
            layout: 'column',
            hideBorders: true,
            autoHeight: true,
            items: [
                this._widthField,
                this._xField,
                this._heightField,
                this._pxField
            ]
        });

        this._widthField.on('blur', this._validateSizes, this);
        this._widthField.on('keyup', this._validateSizes, this);
        this._widthField.on('keyup', this._updateOutSize, this);
        this._heightField.on('blur', this._validateSizes, this);
        this._heightField.on('keyup', this._validateSizes, this);
        this._heightField.on('keyup', this._updateOutSize, this);
        this._dimensionField.on('change', this._validateSizes, this);
        this._dimensionField.on('change', this._resetCropRegion, this);

        // Has to be initialized before cropRegion because cropRegion needs dimensionValue
        if (this.value && this.value.dimension) {
            this._dimensionField.setValue(this.value.dimension);
            this._widthField.setValue(this.value.width);
            this._heightField.setValue(this.value.height);
        } else {
            for (var i in this.dimensions) {
                break;
            }
            this._dimensionField.setValue(i);
        }
        this._enableDisableFields();

        this._configPanel = new Ext.Panel({
            region: 'west',
            bodyStyle: 'padding: 10px',
            width: 200,
            autoScroll: true,
            title: trlKwf('Select dimension'),
            hideBorders: true,
            items: [
                this._dimensionField,
                this._userSelection
            ]
        });

        this._initCropRegion();

        this._cropPanel = new Ext.Panel({
            region: 'center',
            title: trlKwf('Select image region'),
            width: 600,
            height: 200,
            items: [
                this._cropImage
            ]
        });

        this.items = new Ext.FormPanel({
            border: false,
            bodyStyle: 'padding: 10px',
            layout: 'border',
            items: [
                this._configPanel,
                this._cropPanel
            ]
        });
        this.buttons = [
            {
                text: trlKwf('OK'),
                handler: function() {
                    if (this._validateSizes()) {
                        this.value = {
                            dimension: this._dimensionField.getValue(),
                            width: this._widthField.getValue(),
                            height: this._heightField.getValue(),
                            cropData: this._cropImage.getValue()
                        };
                        this.fireEvent('save', this.value);
                        this.close();
                    } else {
                        Ext.Msg.alert(trlKwf('Error'), trlKwf('Please fill the marked fields correctly.'));
                    }
                },
                scope: this
            },{
                text: trlKwf('Cancel'),
                handler: function() {
                    this.close();
                },
                scope: this
            }
        ];

        Kwc.Abstract.Image.DimensionWindow.superclass.initComponent.call(this);
        this._validateSizes();
    },

    _initCropRegion: function ()
    {
        var cropData = null;
        if (this._dimensionField.getValue() == this.value.dimension) {
            // load saved crop-values if not 0
            if (this.value.cropData && this.value.cropData.width > 0 && this.value.cropData.height > 0) {
                cropData = this.value.cropData;
            }
        }

        var outWidth = this._getUserSelectedDimensionWidth();
        var outHeight = this._getUserSelectedDimensionHeight();
        if (outWidth == -1 || outHeight == -1) {
            return;
        }

        var imageWidth = Math.round(this.imageData.imageWidth / this.imageData.imageHandyScaleFactor);
        var imageHeight = Math.round(this.imageData.imageHeight / this.imageData.imageHandyScaleFactor);
        this._cropImage = new Kwc.Abstract.Image.CropImage({
            // call controller to create image with nice size to work with
            src: '/kwf/media/upload/download-handy?uploadId='+this.imageData.uploadId+'&hashKey='+this.imageData.hashKey,
            preserveRatio: this._getPreserveRatio(),
            cls: 'kwc-abstract-image-dimension-window-crop-image',
            outWidth: outWidth,
            outHeight: outHeight,
            cropData: cropData,
            width: imageWidth,
            height: imageHeight,
            style: 'margin-left:'+imageWidth/-2+'px;margin-top:'+imageHeight/-2+'px'
        });

        // Check if smaller than usefull so keep min-width
        var width = this.width;
        if (imageWidth +this._configPanel.width +18 > this.minWidth) {// and border
            width = this._configPanel.width +18 + imageWidth;
        }
        var height = this.height;
        if (imageHeight +98 > this.minHeight) { //titles height
            height = imageHeight +98;
        }
        this.setSize(width, height);
    },

    _getPreserveRatio: function()
    {
        var preserveRatio = false;
        var outWidth = this._getUserSelectedDimensionWidth();
        var outHeight = this._getUserSelectedDimensionHeight();
        var dimension = this.dimensions[this._dimensionField.getValue()];
        if (dimension.cover == true && !(outWidth == 0 || outHeight == 0)) {
            preserveRatio = true;
        }
        return preserveRatio;
    },

    _getUserSelectedDimensionHeight: function ()
    {
        var outHeight = -1;
        var dimension = this.dimensions[this._dimensionField.getValue()];
        if (dimension.height == 'user' && this._heightField.getValue() != '') {
            outHeight = this._heightField.getValue();
        } else if (dimension.height >= 0) {
            outHeight = dimension.height;
        } else if (typeof dimension.height === 'undefined') {
            outHeight = 0;
        } else {
            Ext.Msg.alert(trlKwf('Error'), trlKwf('No height value was set'));
        }
        return outHeight;
    },

    _getUserSelectedDimensionWidth: function ()
    {
        var outWidth = -1;
        var dimension = this.dimensions[this._dimensionField.getValue()];
        if (dimension.width == 'user' && this._widthField.getValue() != '') {
            outWidth = this._widthField.getValue();
        } else if (dimension.width == 'contentWidth') {
            outWidth = 0;
        } else if (dimension.width >= 0) {
            outWidth = dimension.width;
        } else if (typeof dimension.width === 'undefined') {
            outWidth = 0;
        } else {
            Ext.Msg.alert(trlKwf('Error'), trlKwf('No width value was set'));
        }
        return outWidth;
    },

    _resetCropRegion: function (element, value)
    {
        var cropData = null;
        if (value.inputValue == this.value.dimension) {
            cropData = this.value.cropData;
        }
        this.value = {
            dimension: this._dimensionField.getValue(),
            width: this._widthField.getValue(),
            height: this._heightField.getValue(),
            cropData: cropData
        };
        var outWidth = this._getUserSelectedDimensionWidth();
        var outHeight = this._getUserSelectedDimensionHeight();
        if (outWidth == -1 || outHeight == -1) {
            return;
        }
        this._cropImage.outWidth = outWidth;
        this._cropImage.outHeight = outHeight;
        this._cropImage.setCropData(cropData, this._getPreserveRatio());
    },

    _validateSizes: function()
    {
        var dim = this._dimensionField.getValue();
        if (this._widthField.getValue() < 1 && this.dimensions[dim].width == 'user'
            && this._heightField.getValue() < 1 && this.dimensions[dim].height == 'user'
        ) {
            if (this._widthField.getValue() < 1) {
                this._widthField.markInvalid(trlKwf('Width or height must be higher than 0 when using crop or cover.'));
            }
            if (this._heightField.getValue() < 1) {
                this._heightField.markInvalid(trlKwf('Width or height must be higher than 0 when using crop or cover.'));
            }
            return false;
        }

        this._widthField.clearInvalid();
        this._heightField.clearInvalid();

        return true;
    },

    _updateOutSize: function()
    {
        var outWidth = this._widthField.getValue();
        var outHeight = this._heightField.getValue();
        var cropData = this._cropImage.getValue();
        var preserveRatio = false;
        if (outWidth > 0 && outHeight > 0) {
            this._cropImage.outWidth = outWidth;
            this._cropImage.outHeight = outHeight;
            preserveRatio = true;
            cropData.height = outHeight * cropData.width / outWidth;
        }
        this._cropImage.setCropData(cropData, preserveRatio);
    },

    _enableDisableFields: function()
    {
        var userSelection = false;
        for (var i in this.dimensions) {
            var dim = this.dimensions[i];
            if (dim.width == 'user' || dim.height == 'user') {
                userSelection = true;
            }
        }
        if (userSelection) {
            this._userSelection.show();
            var dim = this.dimensions[this._dimensionField.getValue()];
            this._widthField.setDisabled(!(dim && dim.width == 'user'));
            this._heightField.setDisabled(!(dim && dim.height == 'user'));
            this._xField.setDisabled(!(dim && dim.width == 'user')
                    || !(dim && dim.height == 'user'));
            this._pxField.setDisabled(!(dim && dim.width == 'user')
                    || !(dim && dim.height == 'user'));
        } else {
            this._userSelection.hide();
        }
    }
});

Ext.reg('kwc.image.dimensionwindow', Kwc.Abstract.Image.DimensionWindow);
