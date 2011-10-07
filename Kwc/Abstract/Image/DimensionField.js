Ext.namespace('Vpc.Abstract.Image');
Vpc.Abstract.Image.DimensionField = Ext.extend(Ext.form.TriggerField, {
    triggerClass: 'x-form-search-trigger',
    width: 300,
    readOnly: true,
    initComponent: function() {
        Vpc.Abstract.Image.DimensionField.superclass.initComponent.call(this);
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
        Vpc.Abstract.Image.DimensionField.superclass.afterRender.call(this);
        if (this.value) {
            this.setValue(this.value);
        }
    },

    _validateSizes: function()
    {
        var dim = this.dimensionField.getValue();
        if (this.dimensions[dim].scale == 'crop' || this.dimensions[dim].scale == 'bestfit') {
            if (this.widthField.getValue() < 1 && this.dimensions[dim].width == 'user'
                && this.heightField.getValue() < 1 && this.dimensions[dim].height == 'user'
            ) {
                if (this.widthField.getValue() < 1) {
                    this.widthField.markInvalid(trlVps('Width or height must be higher than 0 when using crop or bestfit.'));
                }
                if (this.heightField.getValue() < 1) {
                    this.heightField.markInvalid(trlVps('Width or height must be higher than 0 when using crop or bestfit.'));
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
            this.dimensionField = new Vps.Form.RadioGroup({
                columns: 1,
                hideLabel: true,
                vertical: false,
                items: radios
            });
            this.widthField = new Ext.form.NumberField({
                fieldLabel: trlVps('Width'),
                width: 50,
                enableKeyEvents: true,
                validateOnBlur: false,
                validationEvent: false,
                allowNegative: false
            });
            this.heightField = new Ext.form.NumberField({
                fieldLabel: trlVps('Height'),
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

            this.sizeWindow = new Ext.Window({
                title: trlVps('Image Size'),
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
                            title: trlVps('Size'),
                            items: [
                                this.widthField,
                                this.heightField
                            ]
                        }
                    ]
                }),
                buttons: [{
                    text: trlVps('OK'),
                    handler: function() {
                        if (this._validateSizes()) {
                            this.sizeWindow.hide();
                            this.setValue({
                                dimension: this.dimensionField.getValue(),
                                width: this.widthField.getValue(),
                                height: this.heightField.getValue()
                            });
                        } else {
                            Ext.Msg.alert(trlVps('Error'), trlVps('Please fill the marked fields correctly.'));
                        }
                    },
                    scope: this
                },{
                    text: trlVps('Cancel'),
                    handler: function() {
                        this.sizeWindow.hide();
                    },
                    scope: this
                }]
            });
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
                bestfit: trlVps('Bestfit'),
                crop: trlVps('Crop'),
                deform: trlVps('Deform'),
                original: trlVps('Original')
            };
            if (d.scale && scaleModes[d.scale]) {
                ret += scaleModes[d.scale];
            }
            ret = ret.trim() + ')';
        }
        return ret;
    }
});


Ext.reg('vpc.image.dimensionfield', Vpc.Abstract.Image.DimensionField);
