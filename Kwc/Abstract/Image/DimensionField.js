Ext2.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.DimensionField = Ext2.extend(Ext2.form.Field, {
    _scaleFactor: null,
    resolvedDimensions: null,

    autoEl: {
        tag: 'div',
        cls: 'kwc-abstract-image-dimension',
        children: [{
            tag: 'div',
            cls: 'kwc-abstract-image-dimension-name'
        }]
    },
    imageData: null,

    getValue: function() {
        return this.value || {};
    },

    setValue: function(v) {
        if (v == '') {
            for (i in this.dimensions) {
                v = this.dimensions[i];
                v.dimension = i;
                break;
            }
        }
        this.value = v;
        if (this.rendered) {
            var pixelString = '';
            if (v.dimension) {
                pixelString = Kwc.Abstract.Image.DimensionField.getDimensionPixelString(this.resolvedDimensions[v.dimension], v, this.dpr2Check);
            }
            if (pixelString) {
                this.getEl().child('.kwc-abstract-image-dimension-name').update(trlKwf('At least: ')+pixelString);
            } else {
                this.getEl().child('.kwc-abstract-image-dimension-name').update('&nbsp;');
            }
        }
        this.fireEvent('change', this.value);
    },

    initComponent: function() {
        this.resolvedDimensions = Kwf.clone(this.dimensions);
        Kwc.Abstract.Image.DimensionField.superclass.initComponent.call(this);
    },

    afterRender: function() {
        Kwc.Abstract.Image.DimensionField.superclass.afterRender.call(this);
        this._cropButton = new Ext2.Button({
            text: trlKwf('Edit'),
            cls: 'x2-btn-text-icon kwc-abstract-image-dimension-cropbutton',
            icon: '/assets/silkicons/shape_handles.png',
            renderTo: this.getEl(),
            scope: this,
            handler: this._onButtonClick
        });

        if (this.value) {
            this.setValue(this.value);
        }
    },

    _onButtonClick: function() {
        this._sizeWindow = new Kwc.Abstract.Image.DimensionWindow({
            dimensions: this.resolvedDimensions,
            value: this.getValue(),
            imageData: this.imageData,
            selectDimensionDisabled: this.selectDimensionDisabled,
            _scaleFactor: this._scaleFactor,
            _dpr2Check: this.dpr2Check
        });
        this._sizeWindow.on('save', function(value) {
            this.setValue(value);
            // Height and Width could be set from user. resolvedDimensions have
            // to be updated.
            for (i in this.resolvedDimensions) {
                var dimension = this.dimensions[i];
                var resolvedDimension = this.resolvedDimensions[i];
                if (resolvedDimension.width == 'user') {
                    resolvedDimension.width = dimension.width;
                }
                if (resolvedDimension.height == 'user') {
                    resolvedDimension.height = dimension.height;
                }
            }
        }, this);
        this._sizeWindow.show();
    },

    setContentWidth: function (contentWidth) {
        // ContentWidth is used through this array at
        //  + isValidImageSize (used by DimensionWindow)
        //  + getDimensionPixelString
        //  + getDimensionString (is using getDimensionPixelString)
        this.resolvedDimensions = Kwf.clone(this.dimensions);
        for (i in this.resolvedDimensions) {
            var dimension = this.resolvedDimensions[i];
            if (dimension.width == 'contentWidth') {
                dimension.width = contentWidth;
            }
        }
    },

    setScaleFactor: function (scaleFactor) {
        this._scaleFactor = scaleFactor;
    },

    newImageUploaded: function (value) {
        this._cropButton.setVisible(value != '');
        if (this.imageData != null && this.imageData != "") {
            var dimensionValue = this.getValue();
            if (dimensionValue.cropData) {
                dimensionValue.cropData = null;
                this.setValue(dimensionValue);
            }
        }
        this.imageData = value;
    }
});

Kwc.Abstract.Image.DimensionField.isValidImageSize = function(value, dimensions, scaleFactor, dpr2)
{
    if (!value.cropData)
        return true;
    var dimension = dimensions[value.dimension];
    var width =  dimension.width == 'user' ? value.width : dimension.width;
    var height = dimension.height == 'user' ? value.height : dimension.height;
    var dprFactor = 1;
    if (dpr2) {
        dprFactor = 2;
    }
    if (width * dprFactor > value.cropData.width * scaleFactor
        || height * dprFactor > value.cropData.height * scaleFactor) {
        return false;
    }
    return true;
};

Kwc.Abstract.Image.DimensionField.getDimensionPixelString = function(dimension, v, dpr2)
{
    var dprFactor = 1;
    if (dpr2) dprFactor = 2;
    var width = null;
    if (!isNaN(parseInt(dimension.width))) {
        width = dimension.width;
    } else if (dimension.width == 'user' && v) {
        width = v.width;
    }
    var height = null;
    if (!isNaN(parseInt(dimension.height))) {
        height = dimension.height;
    } else if (dimension.height == 'user' && v) {
        height = v.height;
    }
    height *= dprFactor;
    width *= dprFactor;
    var ret = '';
    if (height && width) {
        ret = width + 'x' + height+'px';
    } else if (height) {
        ret = trlKwf('{0}px high', height);
    } else if (width) {
        ret = trlKwf('{0}px wide', width);
    }
    return ret;
};

Kwc.Abstract.Image.DimensionField.getDimensionString = function(dimension, v, dpr2)
{
    var ret;
    if (!dimension) return;

    if (dimension.text) {
        ret = dimension.text;
    } else {
        ret = '';
    }

    var pixelString = Kwc.Abstract.Image.DimensionField.getDimensionPixelString(dimension, v, dpr2);
    if (ret) {
        if (pixelString) {
            ret += ' ('+pixelString+')';
        }
    } else {
        ret = pixelString;
    }
    if (!ret) ret = '&nbsp;';
    return ret;
};


Ext2.reg('kwc.image.dimensionfield', Kwc.Abstract.Image.DimensionField);
