Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.CropImage = Ext.extend(Ext.BoxComponent, {
    src: null,//image path
    preserveRatio: false,
    width: null,//width of image
    height: null,//height of image
    //values of initial selected region
    cropWidth: null,
    cropHeight: null,
    cropX: null,
    cropY: null,

    minWidth: 50,//min width of crop region
    minHeight: 50,//min height of crop region
    _image: null,

    autoEl: {
        tag: 'div',
        children: [{
            tag: 'div',
            cls: 'kwc-crop-image-wrapper'
        }]
    },

    initComponent: function() {
        Kwc.Abstract.Image.CropImage.superclass.initComponent.call(this);
    },

    getCropData: function() {
        var parent = this.getBox();
        if (!this._image) {
            return;
        }
        var imageBox = this._image.getBox();
        var result = {
            x: (imageBox.x - parent.x),
            y: (imageBox.y - parent.y),
            width: imageBox.width,
            height: imageBox.height
        };
        return result;
    },

    _updateCropRegion: function () {
        var result = this.getCropData();
        this._image.getEl().setStyle({
            'background-position': (-result.x)+'px '+(-result.y)+'px'
        });
    },

    onRender: function(ct, position) {
        Kwc.Abstract.Image.CropImage.superclass.onRender.call(this, ct, position);
        this.el.setStyle({
            'background': 'url('+this.src+') no-repeat left top'
        });

        var wrapEl = this.el.down('.kwc-crop-image-wrapper');
        wrapEl.setSize(this.width, this.height);

        this._image = new Ext.BoxComponent({
            opacity: 1.0,
            autoEl: {
                tag: 'div',
                src: this.src
            },
            renderTo: this.el,
            constrainTo: this.el,
            src: Ext.BLANK_IMAGE_URL,
            x: this.cropX,
            y: this.cropY,
            height: this.height,
            width: this.width,
            style:{
                background: 'url('+this.src+') no-repeat left top'
            }
        });

        var resizer = new Ext.Resizable(this._image.getEl(), {
            handles: 'all',
            pinned: true,
            preserveRatio: this.preserveRatio,
            maxWidth: this.width,
            maxHeight: this.height,
            width: this.cropWidth,
            height: this.cropHeight,
            minWidth: this.minWidth,
            minHeight: this.minHeight
        });
        resizer.getEl().addClass('kwc-crop-image-resizable');
        resizer.on("resize", function() {
            this._updateCropRegion();
            var res = this.getCropData();
            this.fireEvent('changeCrop', this, res);
        }, this);

        var dragDrop = new Ext.dd.DD(this._image.getEl(), '');
        dragDrop.startDrag = (function (x, y) {
            dragDrop.constrainTo(this.el);
            this._image.getEl().setStyle({
                'background': 'transparent'
            });
        }).createDelegate(this);
        dragDrop.endDrag = (function (e) {
            this._updateCropRegion();
            this._image.getEl().setStyle({
                'background-image': 'url('+this.src+')',
                'background-repeat': 'no-repeat'
            });
            this.fireEvent('changeCrop', this, this.getCropData());
        }).createDelegate(this);
    },

    afterRender: function () {
        Kwc.Abstract.Image.CropImage.superclass.afterRender.call(this);
        this._updateCropRegion();
    }
});

Ext.reg('kwc.image.cropimage', Kwc.Abstract.Image.CropImage);
