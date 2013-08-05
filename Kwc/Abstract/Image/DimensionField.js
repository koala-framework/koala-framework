Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.DimensionField = Ext.extend(Ext.form.TriggerField, {
    triggerClass: 'x-form-search-trigger',
    width: 300,
    readOnly: true,
    imageData: null,
    cropData: null,

    initComponent: function() {
        Kwc.Abstract.Image.DimensionField.superclass.initComponent.call(this);
    },
    getValue: function() {
        return this.value || {};
    },

    setValue: function(v) {
        this.value = v;
        if (this.rendered) {
            if (v.dimension) {
                this.setRawValue(this._getDimensionString(v));
            } else {
                this.setRawValue('');
            }
        }
    },
    afterRender: function() {
        Kwc.Abstract.Image.DimensionField.superclass.afterRender.call(this);
        if (this.value) {
            this.setValue(this.value);
        }
        this.findParentByType('kwf.autoform').// TODO: retrieve Upload-field more cleanly
            findByType('kwf.file')[0].on('change', function (el, value) {
                this.imageData = value;
        }, this);
    },

    _validateSizes: function()
    {
        var dim = this.dimensionField.getValue();
        if (this.dimensions[dim].scale == 'crop' || this.dimensions[dim].scale == 'bestfit') {
            if (this.widthField.getValue() < 1 && this.dimensions[dim].width == 'user'
                && this.heightField.getValue() < 1 && this.dimensions[dim].height == 'user'
            ) {
                if (this.widthField.getValue() < 1) {
                    this.widthField.markInvalid(trlKwf('Width or height must be higher than 0 when using crop or bestfit.'));
                }
                if (this.heightField.getValue() < 1) {
                    this.heightField.markInvalid(trlKwf('Width or height must be higher than 0 when using crop or bestfit.'));
                }
                return false;
            }
        }

        this.widthField.clearInvalid();
        this.heightField.clearInvalid();

        return true;
    },

    onTriggerClick: function() {
        if (!this.sizeWindow) {
            var radios = [];
            for (var i in this.dimensions) {
                radios.push({
                    inputValue: i,
                    boxLabel: this._getDimensionString({dimension: i}),
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

            var button = new Ext.Button({
                text: 'Crop Image',
                handler: function() {
                    var width, height;
                    var preserveRatio = false;
                    var cropData = null;
                    if (this.dimensionField.getValue() == this.getValue().dimension) {
                        //TODO load saved crop-values if not 0
                        cropData = this.getValue().cropData;
                    }
                    var dimension = this.dimensions[this.dimensionField.getValue()];
                    if (dimension.height == 'user' && this.heightField.getValue() != '') {
                        height = this.heightField.getValue();
                    } else {
                        //TODO throw exception no value set
                    }

                    if (dimension.width == 'user' && this.widthField.getValue() != '') {
                        width = this.widthField.getValue();
                    } else {
                        //TODO throw exception no value set
                    }

                    if (dimension.scale == 'crop') {
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
                                this.cropData = cw.cropData;
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

            this.sizeWindow = new Ext.Window({
                title: trlKwf('Image Size'),
                closeAction: 'hide',
                modal: true,
                width: 350,
                height: 350,
                layout: 'fit',
                items: new Ext.FormPanel({
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
                        button
                    ]
                }),
                buttons: [{
                    text: trlKwf('OK'),
                    handler: function() {
                        if (this._validateSizes()) {
                            this.sizeWindow.hide();
                            this.setValue({
                                dimension: this.dimensionField.getValue(),
                                width: this.widthField.getValue(),
                                height: this.heightField.getValue(),
                                cropData: this.cropData
                            });
                        } else {
                            Ext.Msg.alert(trlKwf('Error'), trlKwf('Please fill the marked fields correctly.'));
                        }
                    },
                    scope: this
                },{
                    text: trlKwf('Cancel'),
                    handler: function() {
                        this.sizeWindow.hide();
                    },
                    scope: this
                }]
            });
        }

        if (!this.imageData) {
            //TODO better way to find button in panel
            this.sizeWindow.items.items[0].items.items[2].disable();
        } else {
            this.sizeWindow.items.items[0].items.items[2].enable();
        }

        var v = this.getValue();
        if (v && v.dimension) {
            this.dimensionField.setValue(v.dimension);
            this.widthField.setValue(v.width);
            this.heightField.setValue(v.height);
        } else {
            for (var i in this.dimensions) {
                break;
            }
            this.dimensionField.setValue(i);
        }
        this._enableDisableFields();
        this.sizeWindow.show();

        this._validateSizes();
    },

    _enableDisableFields: function()
    {
        var dim = this.dimensions[this.dimensionField.getValue()];
        this.widthField.setDisabled(!(dim && dim.width == 'user'));
        this.heightField.setDisabled(!(dim && dim.height == 'user'));
    },

    _getDimensionString: function(v)
    {
        var ret;
        if (this.dimensions[v.dimension] && this.dimensions[v.dimension].text) {
            ret = this.dimensions[v.dimension].text;
        } else {
            ret = v.dimension;
        }
        if (this.dimensions[v.dimension]) {
            var d = this.dimensions[v.dimension];
            ret += ' (';
            if (d.width == 'user') {
                if (v.width) {
                    ret += String(v.width);
                } else {
                    ret += '?';
                }
            } else if (d.width) {
                ret += String(d.width);
            } else {
                ret += '?';
            }
            ret += 'x';
            if (d.height == 'user') {
                if (v.height) {
                    ret += String(v.height);
                } else {
                    ret += '?';
                }
            } else if (d.height) {
                ret += String(d.height);
            } else {
                ret += '?';
            }
            ret += ' ';
            var scaleModes = {
                bestfit: trlKwf('Bestfit'),
                crop: trlKwf('Crop'),
                deform: trlKwf('Deform'),
                original: trlKwf('Original')
            };
            if (d.scale && scaleModes[d.scale]) {
                ret += scaleModes[d.scale];
            }
            ret = ret.trim() + ')';
        }
        return ret;
    }
});


Ext.reg('kwc.image.dimensionfield', Kwc.Abstract.Image.DimensionField);
