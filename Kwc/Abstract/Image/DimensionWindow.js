Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.DimensionWindow = Ext.extend(Ext.Window, {

    dimensions: null,
    value: null, //contains width, height, cropdata

    title: trlKwf('Image Size'),
    closeAction: 'close',
    modal: true,
    width: 850,
    height: 350,
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
        this.dimensionField = new Kwf.Form.RadioGroup({
            columns: 1,
            hideLabel: true,
            vertical: false,
            items: radios
        });
        this.widthField = new Ext.form.NumberField({
            fieldLabel: trlKwf('Width'),
            width: 50,
            enableKeyEvents: true,
            validateOnBlur: false,
            validationEvent: false,
            allowNegative: false
        });
        this.heightField = new Ext.form.NumberField({
            fieldLabel: trlKwf('Height'),
            width: 50,
            enableKeyEvents: true,
            validateOnBlur: false,
            validationEvent: false,
            allowNegative: false
        });

        this.widthField.on('blur', this._validateSizes, this);
        this.widthField.on('keyup', this._validateSizes, this);
        this.heightField.on('blur', this._validateSizes, this);
        this.heightField.on('keyup', this._validateSizes, this);
        this.dimensionField.on('change', this._validateSizes, this);
        this.dimensionField.on('change', this._resetCropRegion, this);

        // Has to be initialised before cropRegion because cropRegion needs dimensionValue
        if (this.value && this.value.dimension) {
            this.dimensionField.setValue(this.value.dimension);
            this.widthField.setValue(this.value.width);
            this.heightField.setValue(this.value.height);
        } else {
            for (var i in this.dimensions) {
                break;
            }
            this.dimensionField.setValue(i);
        }
        this._enableDisableFields();

        this._initCropRegion();

        this._configPane = new Ext.FormPanel({
            region: 'west',
            layout: 'fit',
            width: 250,
            items: [
                {
                    xtype: 'fieldset',
                    autoHeight: true,
                    title: trlKwf('Size'),
                    items: [
                        this.widthField,
                        this.heightField
                    ]
                },
                this.dimensionField
            ]
        });

        this._cropPane = new Ext.Panel({
            region: 'center',
            layout: 'fit',
            width: 600,
            height: 200,
            items: [
                this._cropImage
            ]
        });

        this.items = new Ext.FormPanel({
            bodyStyle: 'padding: 10px',
            layout: 'border',
            items: [
                this._configPane,
                this._cropPane
            ]
        });
        this.buttons = [
            {
                text: trlKwf('OK'),
                handler: function() {
                    if (this._validateSizes()) {
                        this.value.cropData = this.cropData;
                        this.value = {
                            dimension: this.dimensionField.getValue(),
                            width: this.widthField.getValue(),
                            height: this.heightField.getValue(),
                            cropData: this.value.cropData
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
        if (this.dimensionField.getValue() == this.value.dimension) {
            // load saved crop-values if not 0
            if (this.value.cropData && this.value.cropData.width > 0 && this.value.cropData.height > 0) {
                cropData = this.value.cropData;
            }
        }

        var outWidth = this._getWidth();
        var outHeight = this._getHeight();
        if (outWidth == -1 || outHeight == -1) {
            return;
        }

        var preserveRatio = this._getPreserveRatio();

        this._cropImage = new Kwc.Abstract.Image.CropImage({
            // call controller to create image with nice size to work with
            src: '/kwf/media/upload/download-handy?uploadId='+this.imageData.uploadId+'&hashKey='+this.imageData.hashKey,
            preserveRatio: preserveRatio,
            width: 500,
            height: 300,
            outWidth: outWidth,
            outHeight: outHeight,
            cropData: cropData
        });

        this._cropImage.on('changeCrop', function(cropImageElement, x) {
            this.cropData = x;
        }, this);
        this._cropImage.on('finishedLoading', function (dimensions) {
            this._cropPane.width = dimensions.width;
            this._cropPane.height = dimensions.height;
            this.setSize(250 + 18 + dimensions.width, dimensions.height + 73);
            //TODO handle image height smaller than dimensions-radios
        }, this);
    },

    _getPreserveRatio: function()
    {
        var preserveRatio = false;
        var outWidth = this._getWidth();
        var outHeight = this._getHeight();
        var dimension = this.dimensions[this.dimensionField.getValue()];
        if (dimension.cover == true && !(outWidth == 0 || outHeight == 0)) {
            preserveRatio = true;
        }
        return preserveRatio;
    },

    _getHeight: function ()
    {
        var outHeight = -1;
        var dimension = this.dimensions[this.dimensionField.getValue()];
        if (dimension.height == 'user' && this.heightField.getValue() != '') {
            outHeight = this.heightField.getValue();
        } else if (dimension.height >= 0) {
            outHeight = dimension.height;
        } else if (typeof dimension.height === 'undefined') {
            outHeight = 0;
        } else {
            Ext.Msg.alert(trlKwf('Error'), trlKwf('No height value was set'));
        }
        return outHeight;
    },

    _getWidth: function ()
    {
        var outWidth = -1;
        var dimension = this.dimensions[this.dimensionField.getValue()];
        if (dimension.width == 'user' && this.widthField.getValue() != '') {
            outWidth = this.widthField.getValue();
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
            dimension: this.dimensionField.getValue(),
            width: this.widthField.getValue(),
            height: this.heightField.getValue(),
            cropData: cropData
        };
        var outWidth = this._getWidth();
        var outHeight = this._getHeight();
        if (outWidth == -1 || outHeight == -1) {
            return;
        }
        this._cropImage.outWidth = outWidth;
        this._cropImage.outHeight = outHeight;
        this._cropImage.setCropData(cropData, this._getPreserveRatio());
    },

    _validateSizes: function()
    {
        var dim = this.dimensionField.getValue();
        if (this.widthField.getValue() < 1 && this.dimensions[dim].width == 'user'
            && this.heightField.getValue() < 1 && this.dimensions[dim].height == 'user'
        ) {
            if (this.widthField.getValue() < 1) {
                this.widthField.markInvalid(trlKwf('Width or height must be higher than 0 when using crop or cover.'));
            }
            if (this.heightField.getValue() < 1) {
                this.heightField.markInvalid(trlKwf('Width or height must be higher than 0 when using crop or cover.'));
            }
            return false;
        }

        this.widthField.clearInvalid();
        this.heightField.clearInvalid();

        return true;
    },

    _enableDisableFields: function()
    {
        var dim = this.dimensions[this.dimensionField.getValue()];
        this.widthField.setDisabled(!(dim && dim.width == 'user'));
        this.heightField.setDisabled(!(dim && dim.height == 'user'));
    }
});

Ext.reg('kwc.image.dimensionwindow', Kwc.Abstract.Image.DimensionWindow);
