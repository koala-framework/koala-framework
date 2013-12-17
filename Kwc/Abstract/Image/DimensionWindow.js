Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.DimensionWindow = Ext.extend(Ext.Window, {

    _scaleFactor: null,
    _dpr2Check: false,

    dimensions: null,
    value: null, //contains width, height, cropdata

    title: trlKwf('Edit'),
    closeAction: 'close',
    modal: true,
    minWidth: 488,
    minHeight: 250,
    width: 488,
    height: 250,
    layout: 'fit',
    resizable: false,
    _showUserSelection: null,

    initComponent: function() {
        var radios = [];
        for (var i in this.dimensions) {
            radios.push({
                inputValue: i,
                boxLabel: Kwc.Abstract.Image.DimensionField.getDimensionString(this.dimensions[i]),
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
            cls: 'kwc-abstract-image-dimension-window-userselection',
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

        var resolutionField = new Ext.BoxComponent({
            autoEl: {
                html: this.imageData.imageWidth+'x'+this.imageData.imageHeight+'px',
                hideBorders: true
            }
        });
        var filenameField = new Ext.BoxComponent({
            autoEl: {
                html: this.imageData.filename+'.'+this.imageData.extension,
                hideBorders: true
            }
        });
        var filesizeField = new Ext.BoxComponent({
            autoEl: {
                html: Math.round((this.imageData.fileSize/10.24))/100+' KB',
                hideBorders: true
            }
        });
        var imageDataField = {
            xtype: 'fieldset',
            autoHeight: true,
            cls: 'kwc-abstract-image-dimension-window-imagedata',
            hideBorders: false,
            title: trlKwf('Image Data'),
            items: [
                filenameField,
                resolutionField,
                filesizeField
            ]
        };

        this._configPanel = new Ext.Panel({
            region: 'west',
            bodyStyle: 'padding: 10px',
            width: 270,
            autoScroll: true,
            title: trlKwf('Dimension'),
            items: [
                this._dimensionField,
                this._userSelection,
                imageDataField
            ]
        });
        if (this.selectDimensionDisabled) {
            this._configPanel.disable();
        }

        this._initCropRegion();

        var cropPanelItems = new Array();
        if (this._cropImage.isCropDisabled()) {
            // show information for not able to crop
            var cropDisabledInfo = new Ext.BoxComponent({
                autoEl: {
                    tag: 'div',
                    cls: 'information',
                    html: trlKwf('Croping is not possible because image is too small.')
                }
            });
            cropPanelItems.add(cropDisabledInfo);
        }
        cropPanelItems.add(this._cropImage);

        this._cropPanel = new Ext.Panel({
            tbar: [{
                text: trlKwf('Reset'),
                handler: function () {
                    this._cropImage.setCropData(null, this._getPreserveRatio());
                },
                cls:"x-btn-text-icon",
                hideLabel: true,
                icon: '/assets/silkicons/arrow_out.png',
                tooltip: trlKwf('Reset to default (maximum)'),
                scope: this
            }],
            cls: 'kwc-abstract-image-dimension-window-crop-panel',
            region: 'center',
            title: trlKwf('Image region'),
            width: 600,
            height: 200,
            items: cropPanelItems
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
        this._cropImage.on('cropChanged', function (cropData) {
            var value = {
                dimension: this._dimensionField.getValue(),
                width: this._widthField.getValue(),
                height: this._heightField.getValue(),
                cropData: cropData
            };
            var scaleFactor = this._scaleFactor;
            if (this._dpr2Check) scaleFactor /= 2;
            if (!Kwc.Abstract.Image.DimensionField.checkImageSize(value, this.dimensions, scaleFactor)) {
                for (i in this._dimensionField.items.items) {
                    var item = this._dimensionField.items.items[i];
                    if (item.checked) {
                        item.container.addClass('error');
                    } else if (item.container) {
                        item.container.removeClass('error');
                    }
                }
                this._cropImage.getEl().child('.kwc-abstract-image-crop-image-wrapper').addClass('error');
            } else {
                for (i in this._dimensionField.items.items) {
                    var item = this._dimensionField.items.items[i];
                    if (item.container) {
                        item.container.removeClass('error');
                    }
                }
                this._cropImage.getEl().child('.kwc-abstract-image-crop-image-wrapper').removeClass('error');
            }
        }, this);

        // Check if smaller than usefull so keep min-width
        var width = this.width;
        if (imageWidth +this._configPanel.width +18 > this.minWidth) {// and border
            width = this._configPanel.width +18 + imageWidth;
        }
        var height = this.height;
        if (imageHeight +98 + 26 > this.minHeight) { //titles height + toolbar height
            height = imageHeight +98 +26;
        }
        this.setSize(width, height);
    },

    _getPreserveRatio: function()
    {
        var preserveRatio = false;
        var outWidth = this._getUserSelectedDimensionWidth();
        var outHeight = this._getUserSelectedDimensionHeight();
        var dimension = this.dimensions[this._dimensionField.getValue()];
        if (dimension.cover == true && !(outWidth <= 0 || outHeight <= 0)) {
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
        }
        return outWidth;
    },

    _resetCropRegion: function (element, value)
    {
        //Change to cropData = null to reset selection on change
        var cropData = this._cropImage.getValue();
        if (value.inputValue == this.value.dimension) {
            cropData = this.value.cropData;
        }
        var width = this._getUserSelectedDimensionWidth();
        var height = this._getUserSelectedDimensionHeight();
        this.value = {
            dimension: this._dimensionField.getValue(),
            width: width,
            height: height,
            cropData: cropData
        };
        this._cropImage.outWidth = width;
        this._cropImage.outHeight = height;
        this._cropImage.setCropData(cropData, this._getPreserveRatio());
    },

    _validateSizes: function()
    {
        var dim = this._dimensionField.getValue();
        if (this._widthField.getValue() < 1 && this.dimensions[dim].width == 'user'
            && this._heightField.getValue() < 1 && this.dimensions[dim].height == 'user'
        ) {
            if (this._widthField.getValue() < 1) {
                this._widthField.markInvalid(trlKwf('Width or height must be higher than 0.'));
            }
            if (this._heightField.getValue() < 1) {
                this._heightField.markInvalid(trlKwf('Width or height must be higher than 0.'));
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
        this._cropImage.outWidth = outWidth;
        this._cropImage.outHeight = outHeight;
        if (outWidth > 0 && outHeight > 0) {
            preserveRatio = true;
        }
        this._cropImage.setCropData(cropData, preserveRatio);
    },

    _enableDisableFields: function()
    {
        if (this._showUserSelection == null) {
            this._showUserSelection = false;
            for (var i in this.dimensions) {
                var dim = this.dimensions[i];
                if (dim.width == 'user' || dim.height == 'user'
                    || dim.showUserSelectionWidth || dim.showUserSelectionHeight
                ) {
                    if (dim.width == 'user')
                        dim.showUserSelectionWidth = true;
                    if (dim.height == 'user')
                        dim.showUserSelectionHeight = true;
                    this._showUserSelection = true;
                }
            }
        }
        if (this._showUserSelection) {
            this._userSelection.show();
            var dim = this.dimensions[this._dimensionField.getValue()];
            if (!dim) return;
            this._widthField.setDisabled(!dim.showUserSelectionWidth);
            this._heightField.setDisabled(!dim.showUserSelectionHeight);
            this._xField.setDisabled(!dim.showUserSelectionWidth
                    || !dim.showUserSelectionHeight);
            this._pxField.setDisabled(!dim.showUserSelectionWidth
                    || !dim.showUserSelectionHeight);
        } else {
            this._userSelection.hide();
        }
    }
});

Ext.reg('kwc.image.dimensionwindow', Kwc.Abstract.Image.DimensionWindow);
