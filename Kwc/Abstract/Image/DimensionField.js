Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.DimensionField = Ext.extend(Ext.form.Field, {
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
        this.value = v;
        if (this.rendered) {
            if (v.dimension) {
                this.getEl().child('.kwc-abstract-image-dimension-name').update(Kwc.Abstract.Image.DimensionField.getDimensionString(this.dimensions, v));
            } else {
                this.getEl().child('.kwc-abstract-image-dimension-name').update('');
            }
        }
        this.fireEvent('change', this.value);
    },

    afterRender: function() {
        Kwc.Abstract.Image.DimensionField.superclass.afterRender.call(this);
        this._cropButton = new Ext.Button({
            disabled: true,
            text: trlKwf('Configure'),
            cls: 'x-btn-text-icon',
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
        var sizeWindow = new Kwc.Abstract.Image.DimensionWindow({
            dimensions: this.dimensions,
            value: this.getValue(),
            imageData: this.imageData
        });
        sizeWindow.on('save', function(value) {
            this.setValue(value);
        }, this);
        sizeWindow.show();
    },

    newImageUploaded: function (value) {
        this._cropButton.setDisabled(value == '');
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

Kwc.Abstract.Image.DimensionField.getDimensionString = function(dimensions, v)
{
    var ret;
    var hasName = true;
    if (dimensions[v.dimension] && dimensions[v.dimension].text) {
        ret = dimensions[v.dimension].text;
    } else if (dimensions[v.dimension]) {
        ret = '';
        hasName = false;
    } else {
        ret = v.dimension;
    }
    if (dimensions[v.dimension]) {
        var d = dimensions[v.dimension];
        if (hasName) {
            ret += ' (';
        }
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
        ret += ' x ';
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
        ret += ' px, ';
        if (d.cover) {
            ret += trlKwf(' cover');
        } else {
            ret += trlKwf(' don\'t cover');
        }
        if (hasName) {
            ret = ret.trim() + ')';
        }
    }
    return ret;
};


Ext.reg('kwc.image.dimensionfield', Kwc.Abstract.Image.DimensionField);
