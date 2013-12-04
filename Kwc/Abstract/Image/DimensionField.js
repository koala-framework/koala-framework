Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.DimensionField = Ext.extend(Ext.form.Field, {
    _scaleFactor: null,

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
            if (v.dimension) {
                this.getEl().child('.kwc-abstract-image-dimension-name').update(trlKwf('Required: ')+Kwc.Abstract.Image.DimensionField.getDimensionString(this.dimensions[v.dimension], v));
            } else {
                this.getEl().child('.kwc-abstract-image-dimension-name').update('&nbsp;');
            }
        }
        this.fireEvent('change', this.value);
    },

    afterRender: function() {
        Kwc.Abstract.Image.DimensionField.superclass.afterRender.call(this);
        this._cropButton = new Ext.Button({
            text: trlKwf('Edit'),
            cls: 'x-btn-text-icon kwc-abstract-image-dimension-cropbutton',
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
            dimensions: this.dimensions,
            value: this.getValue(),
            imageData: this.imageData
        });
        this._sizeWindow.on('save', function(value) {
            this.setValue(value);
        }, this);
        this._sizeWindow.setScaleFactor(this._scaleFactor);
        this._sizeWindow.show();
    },

//    setContentWidth: function (contentWidth) {
//        for (i in this.dimensions) {
//            var dimension = this.dimensions[i];
//            if (dimension.width == 'contentWidth') {
//                dimension.width = contentWidth;
//            }
//        }
//    },

    setScaleFactor: function (scaleFactor) {
        this._scaleFactor = scaleFactor;
        if (this._sizeWindow)
            this._sizeWindow.setScaleFactor(scaleFactor);
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

Kwc.Abstract.Image.DimensionField.checkImageSize = function(value, dimensions, scaleFactor)
{
    if (!value.cropData)
        return true;
    var dimension = dimensions[value.dimension];
    dimension.width = dimension.width == 'user' ? value.width : dimension.width;
    dimension.height = dimension.height == 'user' ? value.height : dimension.height;
    if (dimension.width > value.cropData.width * scaleFactor
        || dimension.height > value.cropData.height * scaleFactor) {
        return false;
    }
    return true;
};

Kwc.Abstract.Image.DimensionField.getDimensionString = function(dimension, v)
{
    var ret;
    if (!dimension) return;

    if (dimension.text) {
        ret = dimension.text;
    } else {
        ret = '';
    }
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

    if (height && width) {
        if (ret) {
            ret += ' ('+width+'x'+height+'px)';
        } else {
            ret = width + 'x' + height+'px';
        }
    }
    if (!ret) ret = '&nbsp;';
    return ret;
};


Ext.reg('kwc.image.dimensionfield', Kwc.Abstract.Image.DimensionField);
