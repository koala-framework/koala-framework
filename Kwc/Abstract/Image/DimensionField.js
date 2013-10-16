Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.DimensionField = Ext.extend(Ext.form.TriggerField, {
    triggerClass: 'x-form-search-trigger',
    width: 300,
    readOnly: true,
    imageData: null,

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
                this.setRawValue(Kwc.Abstract.Image.DimensionField.getDimensionString(this.dimensions, v));
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
                if (this.imageData != null && this.imageData != "") {
                    var dimensionValue = this.getValue();
                    if (dimensionValue.cropData) {
                        dimensionValue.cropData.x = null;
                        dimensionValue.cropData.y = null;
                        dimensionValue.cropData.width = null;
                        dimensionValue.cropData.height = null;
                        this.setValue(dimensionValue);
                    }
                }
                this.imageData = value;
        }, this);
    },

    onTriggerClick: function() {
        var sizeWindow = new Kwc.Abstract.Image.DimensionWindow({
            dimensions: this.dimensions,
            value: this.getValue(),
            imageData: this.imageData
        });
        sizeWindow.on('save', function(value) {
            this.setValue(value);
        }, this);
        sizeWindow.show();
    }
});

Kwc.Abstract.Image.DimensionField.getDimensionString = function(dimensions, v)
{
    var ret;
    if (dimensions[v.dimension] && dimensions[v.dimension].text) {
        ret = dimensions[v.dimension].text;
    } else {
        ret = v.dimension;
    }
    if (dimensions[v.dimension]) {
        var d = dimensions[v.dimension];
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
        if (d.cover) {
            ret += trlKwf('Crop');
        } else {
            ret += trlKwf("don't Crop");
        }
        ret = ret.trim() + ')';
    }
    return ret;
};


Ext.reg('kwc.image.dimensionfield', Kwc.Abstract.Image.DimensionField);
