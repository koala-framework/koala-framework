Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.DimensionWindow = Ext.extend(Ext.Window, {

    dimensions: null,
    value: null, //contains width, height, cropdata

    title: trlKwf('Image Size'),
    closeAction: 'close',
    modal: true,
    width: 350,
    height: 350,
    layout: 'fit',

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

        this.cropButton = new Ext.Button({
            id: 'kwf-crop-button',
            text: trlKwf('Crop Image'),
            handler: function() {
                var width, height;
                var preserveRatio = false;
                var cropData = null;
                if (this.dimensionField.getValue() == this.value.dimension) {
                    // load saved crop-values if not 0
                    if (this.value.cropData.width > 0 && this.value.cropData.height > 0) {
                        cropData = this.value.cropData;
                    }
                }

                var dimension = this.dimensions[this.dimensionField.getValue()];
                if (dimension.height == 'user' && this.heightField.getValue() != '') {
                    height = this.heightField.getValue();
                } else if (dimension.height >= 0) {
                    height = dimension.height;
                } else {
                    Ext.Msg.alert(trlKwf('Error'), trlKwf('No height value was set'));
                    return;
                }

                if (dimension.width == 'user' && this.widthField.getValue() != '') {
                    width = this.widthField.getValue();
                } else if (dimension.width == 'contentWidth') {
                    width = 0;
                } else if (dimension.width >= 0) {
                    width = dimension.width;
                } else {
                    Ext.Msg.alert(trlKwf('Error'), trlKwf('No width value was set'));
                    return;
                }

                if (dimension.cover == true && !(width == 0 || height == 0)) {
                    preserveRatio = true;
                }

                // call controller to create image with nice size to work with
                var imageURL = '/kwf/media/upload/download-handy?uploadId='+this.imageData.uploadId+'&hashKey='+this.imageData.hashKey;
                var cw = new Kwc.Abstract.Image.CropWindow({
                    imageUrl: imageURL,
                    preserveRatio: preserveRatio,
                    outWidth: width,
                    outHeight: height,
                    cropData: cropData,
                    buttons: [{
                        text: trlKwf('OK'),
                        handler: function() {
                            this.value.cropData = cw.cropData;
                            cw.close();
                        },
                        scope: this
                    },{
                        text: trlKwf('Cancel'),
                        handler: function() {
                            cw.close();
                        },
                        scope: this
                    }]
                });
                cw.show();
            },
            scope: this
        });

        this.dimensionField.on('change', function () {
            var dimension = this.dimensions[this.dimensionField.getValue()];
            if (dimension.width == 0 && dimension.height == 0) {
                this.cropButton.disable();
            } else {
                this.cropButton.enable();
            }
        }, this);

        this.items = new Ext.FormPanel({
            bodyStyle: 'padding: 10px',
            items: [
                this.dimensionField,
                {
                    xtype: 'fieldset',
                    autoHeight: true,
                    title: trlKwf('Size'),
                    items: [
                        this.widthField,
                        this.heightField
                    ]
                },
                this.cropButton
            ]
        });
        this.buttons = [
            {
                text: trlKwf('OK'),
                handler: function() {
                    if (this._validateSizes()) {
                        this.hide();
                        this.value = {
                            dimension: this.dimensionField.getValue(),
                            width: this.widthField.getValue(),
                            height: this.heightField.getValue(),
                            cropData: this.value.cropData
                        };
                        this.fireEvent('save', this.value);
                    } else {
                        Ext.Msg.alert(trlKwf('Error'), trlKwf('Please fill the marked fields correctly.'));
                    }
                },
                scope: this
            },{
                text: trlKwf('Cancel'),
                handler: function() {
                    this.hide();
                },
                scope: this
            }
        ];

        if (!this.imageData) {
            this.cropButton.disable();
        } else {
            this.cropButton.enable();
        }

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
        Kwc.Abstract.Image.DimensionWindow.superclass.initComponent.call(this);
        this._validateSizes();
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
