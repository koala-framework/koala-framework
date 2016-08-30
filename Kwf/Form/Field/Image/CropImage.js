Ext2.namespace('Kwf.Form.Field.Image');
Kwf.Form.Field.Image.CropImage = Ext2.extend(Ext2.BoxComponent, {
    src: null,//image path
    width: 0,//width of image,
    height: 0,//height of image
    outWidth: null,
    outHeight: null,
    //values of initial selected region
    _cropData: null,
    _minImageSize: 64,

    minWidth: 52,//min width of crop region
    minHeight: 52,//min height of crop region
    _image: null,
    _userSelectedCropRegion: null,

    _centerHandle: '<div class="handle {position}"></div>',

    autoEl: {
        tag: 'div',
        children: [{
            tag: 'div',
            cls: 'kwf-form-field-image-crop-image-wrapper'
        }]
    },

    getValue: function () {
        if (this._userSelectedCropRegion) {
            return this._getCropData();
        }
        return null;
    },

    /**
     * Reads width, height, x and y from controls
     */
    _getCropData: function() {
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
        var resetPosition = false;
        if (result.x < 0 || result.y < 0) {
            resetPosition = true;
            if (result.x < 0) {
                result.x = 0;
            }
            if (result.y < 0) {
                result.y = 0;
            }
        }
        var width = this._image.getEl().parent().getWidth();
        var height = this._image.getEl().parent().getHeight();
        if (result.x + result.width > width) {
            resetPosition = true;
            result.x = width - result.width;
        }
        if (result.y + result.height > height) {
            resetPosition = true;
            result.y = height - result.height;
        }
        if (resetPosition) {
            this._image.getEl().setStyle('left', result.x+'px');
            this._image.getEl().setStyle('top', result.y+'px');
        }
        return result;
    },

    _updateCropRegionImage: function () {
        var result = this._getCropData();
        this._image.getEl().setStyle({
            'background-position': (-result.x)+'px '+(-result.y)+'px'
        });
    },

    isCropDisabled: function () {
        return this.width < this._minImageSize
            || this.height < this._minImageSize;
    },

    initComponent: function () {
        Kwf.Form.Field.Image.CropImage.superclass.initComponent.call(this);
        if (this.cropData) {
            this._userSelectedCropRegion = {
                x: this.cropData.x,
                y: this.cropData.y,
                width: this.cropData.width,
                height: this.cropData.height
            };
        }
    },

    onRender: function(ct, position) {
        Kwf.Form.Field.Image.CropImage.superclass.onRender.call(this, ct, position);
        this.el.setStyle({
            'background': 'url('+this.src+') no-repeat left top'
        });

        this._wrapEl = this.el.down('.kwf-form-field-image-crop-image-wrapper');
        this._wrapEl.setSize(this.width, this.height);

        this._image = new Ext2.BoxComponent({
            opacity: 1.0,
            autoEl: {
                tag: 'div',
                src: this.src
            },
            renderTo: this.el,
            src: Ext2.BLANK_IMAGE_URL,
            x: 0,
            y: 0,
            height: this.height,
            width: this.width,
            style:{
                background: 'url('+this.src+') no-repeat left top'
            }
        });

        this._resizer = new Kwf.Utils.Resizable(this._image.getEl(), {
            handles: 'all',
            pinned: true,
            constrainTo: this.getEl(), // TODO improve because it's possible to get 3px or so out of image...
            width: 0,
            height: 0,
            minWidth: this.minWidth,
            minHeight: this.minHeight,
            maxWidth: this.width,
            maxHeight: this.height,
            transparent: true
        });

        this._resizer.getEl().addClass('kwf-form-field-image-crop-image-resizable');
        this._resizer.on('beforeresize', function () {
            this._hideCropShadow();
        }, this);
        this._resizer.on("resize", function() {
            var res = this._getCropData();
            this._userSelectedCropRegion = res;
            this._updateCropRegionImage();
            this._showCropShadow();
            this.fireEvent('cropChanged', res);
        }, this);

        var dragDrop = new Ext2.dd.DD(this._image.getEl(), '');
        dragDrop.startDrag = (function (x, y) {
            this._hideCropShadow();
            dragDrop.constrainTo(this.getEl());
            this._image.getEl().setStyle({
                'background': 'transparent'
            });
        }).createDelegate(this);
        dragDrop.endDrag = (function (e) {
            this._userSelectedCropRegion = this._getCropData();
            this._updateCropRegionImage();
            this._image.getEl().setStyle({
                'background-image': 'url('+this.src+')',
                'background-repeat': 'no-repeat'
            });
            this._showCropShadow();
        }).createDelegate(this);

        if (this.isCropDisabled()) { // Disable crop because image too small
            this._resizer.enabled = false;
            this._resizer.getEl().removeClass('kwf-form-field-image-crop-image-resizable');
            this._resizer.getEl().addClass('kwf-form-field-image-crop-image-resizable-disabled');
            dragDrop.lock();
        } else {
            this._resizer.enabled = true;
            this._resizer.getEl().addClass('kwf-form-field-image-crop-image-resizable');
            this._resizer.getEl().removeClass('kwf-form-field-image-crop-image-resizable-disabled');
            dragDrop.unlock();
        }
    },

    _hideCropShadow: function () {
        var wrapper = this.getEl().child('.kwf-form-field-image-crop-image-wrapper');
        wrapper.addClass('crop-changing');
    },

    _showCropShadow: function () {
        var wrapper = this.getEl().child('.kwf-form-field-image-crop-image-wrapper');
        wrapper.removeClass('crop-changing');
    },

    afterRender: function () {
        this._styleHandles();
        Kwf.Form.Field.Image.CropImage.superclass.afterRender.call(this);

        // Loading-Mask while loading down-sampled image
        this.getEl().mask(trlKwf('Loading image'), 'x2-mask-loading');
        var imgLoad = new Image();
        imgLoad.onerror = (function() {
            this.getEl().unmask();
            Ext2.Msg.alert(trlKwf('Error'), trlKwf("Couldn't load image."));
        }).createDelegate(this);

        imgLoad.onload = (function(){
            this.getEl().unmask();
        }).createDelegate(this);
        imgLoad.src = this.src;

        this._updateCropRegionImage();
    },

    setCropDataAndPreserveRatio: function (cropData, preserveRatio)
    {
        if (this.outWidth == -1) this.outWidth = 0;
        if (this.outHeight == -1) this.outHeight = 0;
        if (!cropData) { // calculate default selection
            this._userSelectedCropRegion = null;
            cropData = this._generateDefaultCrop(preserveRatio);
        }
        if (this.outWidth != 0 && this.outHeight != 0
            && this.outWidth / this.outHeight != cropData.width / cropData.heigth
        ) {
            if (this._userSelectedCropRegion) {
                // Get saved user selected crop-region as base for recalculating
                // width and height are set directly because else object is referenced
                cropData.height = this._userSelectedCropRegion.height;
                cropData.width = this._userSelectedCropRegion.width;
                var width = cropData.height * this.outWidth / this.outHeight;
                var height = cropData.width * this.outHeight / this.outWidth;
                if (width < this.width) {
                    cropData.width = width;
                } else if (height < this.height) {
                    cropData.height = height;
                }
            } else {
                cropData = this._generateDefaultCrop(preserveRatio);
            }
        }
        this._cropData = cropData;
        this._image.setPosition(cropData.x, cropData.y);
        this._resizer.preserveRatio = preserveRatio;
        if (cropData.width != null && cropData.height != null) {
            var backup = this._userSelectedCropRegion;
            this._resizer.resizeTo(cropData.width, cropData.height);
            this._userSelectedCropRegion = backup;
        }
    },

    _generateDefaultCrop: function (preserveRatio) {
        var cropData = {};
        cropData.x = 0;
        cropData.y = 0;
        cropData.width = this.width;
        cropData.height = this.height;
        if (preserveRatio) {
            cropData = Kwf.Form.Field.Image.CropImage
                .calculateDefaultCrop(this.outWidth, this.outHeight, this.width, this.height);
        }
        return cropData;
    },

    _styleHandles: function() {
        if (this._centerHandle) {
            if (!(this._centerHandle instanceof Ext2.XTemplate)) {
                this._centerHandle = new Ext2.XTemplate(this._centerHandle);
            }
            this._centerHandle.compile();
        }

        var middleHandles = [
             this._resizer.west.el,
             this._resizer.east.el,
             this._resizer.north.el,
             this._resizer.south.el
        ];
        for (var i = 0; i < middleHandles.length; i++) {
            middleHandles[i].setStyle('background-image', 'none');
            middleHandles[i].setStyle('opacity', '1');
        }

        this._setCssClassesForHandler(this._resizer.west.el, 'west');
        this._setCssClassesForHandler(this._resizer.east.el, 'east');
        this._setCssClassesForHandler(this._resizer.north.el, 'north');
        this._setCssClassesForHandler(this._resizer.south.el, 'south');
    },

    _setCssClassesForHandler: function (el, side) {
        this._centerHandle.append(el, {
            position: 'dashedline'
        });
        this._centerHandle.append(el, {
            position: side
        });
        if (side == 'west' || side == 'east') {
            this._centerHandle.append(el, {
                position: 'north'+side
            });
            this._centerHandle.append(el, {
                position: 'south'+side
            });
        } else {
            this._centerHandle.append(el, {
                position: side+'west'
            });
            this._centerHandle.append(el, {
                position: side+'east'
            });
        }
    }
});
Kwf.Form.Field.Image.CropImage.calculateDefaultCrop = function (outWidth, outHeight, width, height) {
    var cropData = {
        width: null,
        height: null,
        x: null,
        y: null
    };
    if (height / outHeight > width / outWidth) {
        // orientate on width
        cropData.width = width;
        cropData.height = outHeight * width / outWidth;
        cropData.y = (height - cropData.height)/2;
        cropData.x = 0;
    } else {
        // orientate on height
        cropData.height = height;
        cropData.width = outWidth * height / outHeight;
        cropData.x = (width - cropData.width)/2;
        cropData.y = 0;
    }
    return cropData;
};

Ext2.reg('kwf.form.field.image.cropimage', Kwf.Form.Field.Image.CropImage);
