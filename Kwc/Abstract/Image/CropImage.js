Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.CropImage = Ext.extend(Ext.BoxComponent, {
    src: null,//image path
    preserveRatio: false,
    width: 0,//width of image,
    height: 0,//height of image
    outWidth: null,
    outHeight: null,
    //values of initial selected region
    cropData: null,

    minWidth: 52,//min width of crop region
    minHeight: 52,//min height of crop region
    _image: null,
    _cropRegionChanged: false,
    _ignoreRegionChangeAction: false,

    _centerHandle: '<div class="handle {position}"></div>',

    autoEl: {
        tag: 'div',
        children: [{
            tag: 'div',
            cls: 'kwc-crop-image-wrapper'
        }]
    },

    getValue: function () {
        if (this._cropRegionChanged) {
            return this.getCropData();
        }
        return null;
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

        this._wrapEl = this.el.down('.kwc-crop-image-wrapper');
        this._wrapEl.setSize(0, 0);

        this._image = new Ext.BoxComponent({
            opacity: 1.0,
            autoEl: {
                tag: 'div',
                src: this.src
            },
            renderTo: this.el,
            src: Ext.BLANK_IMAGE_URL,
            x: 0,
            y: 0,
            height: 0,
            width: 0,
            style:{
                background: 'url('+this.src+') no-repeat left top'
            }
        });
        this._resizer = new Ext.Resizable(this._image.getEl(), {
            handles: 'all',
            pinned: true,
            preserveRatio: this.preserveRatio,
            constrainTo: this.getEl(), // TODO improve because it's possible to get 3px or so out of image...
            width: 0,
            height: 0,
            minWidth: this.minWidth,
            minHeight: this.minHeight,
            transparent: true
        });

        this._resizer.getEl().addClass('kwc-crop-image-resizable');
        this._resizer.on("resize", function() {
            if (this._ignoreRegionChangeAction) {
                this._ignoreRegionChangeAction = false;
            } else {
                this._cropRegionChanged = true;
            }
            this._updateCropRegionImage();
            var res = this.getCropData();
        }, this);

        var dragDrop = new Ext.dd.DD(this._image.getEl(), '');
        dragDrop.startDrag = (function (x, y) {
            var wrapper = this.getEl().child('.kwc-crop-image-wrapper');
            wrapper.imageSrcBackup = wrapper.getStyle('background-image');
            wrapper.setStyle({
                'background-image': 'none'
            });
            dragDrop.constrainTo(this.el);
            this._image.getEl().setStyle({
                'background': 'transparent'
            });
        }).createDelegate(this);
        dragDrop.endDrag = (function (e) {
            this._cropRegionChanged = true;
            this._updateCropRegionImage();
            this._image.getEl().setStyle({
                'background-image': 'url('+this.src+')',
                'background-repeat': 'no-repeat'
            });
            var wrapper = this.getEl().child('.kwc-crop-image-wrapper');
            wrapper.setStyle({
                'background-image': wrapper.imageSrcBackup
            });
        }).createDelegate(this);
    },

    afterRender: function () {
        this._styleHandles();
        Kwc.Abstract.Image.CropImage.superclass.afterRender.call(this);
        this.getEl().mask(trlKwf('Loading image'), 'x-mask-loading');
        var imgLoad = new Image();
        imgLoad.onerror = (function() {
            this.getEl().unmask();
            Ext.Msg.alert(trlKwf('Error'), trlKwf("Couldn't load image."));
        }).createDelegate(this);
        imgLoad.onload = (function(){
            this.getEl().unmask();
            this.height = imgLoad.height;
            this.width = imgLoad.width;

            this._setImageSize(this.width, this.height);
            this.setSize(this.width, this.height);
            this.setCropData(this.cropData, this.preserveRatio);

            var params = {
                'width': this.width,
                'height': this.height
            };
            this.fireEvent('finishedLoading', this, params);
        }).createDelegate(this);
        imgLoad.src = this.src;

        this._updateCropRegionImage();
    },

    setCropData: function (cropData, preserveRatio)
    {
        if (!cropData
            || cropData.width == 0 || cropData.width == null
            || cropData.height == 0 || cropData.height == null
            || (cropData.width > this.width && this.width != 0)
            || (cropData.height > this.height && this.height != 0)
        ) { // calculate default selection
            cropData = {};
            cropData.x = 0;
            cropData.y = 0;
            cropData.width = this.width;
            cropData.height = this.height;
            if (preserveRatio) {
                if (this.height / this.outHeight > this.width / this.outWidth) {
                    // orientate on width
                    cropData.height = this.outHeight * this.width / this.outWidth;
                    cropData.y = (this.height - cropData.height)/2;
                } else {
                    // orientate on height
                    cropData.width = this.outWidth * this.height / this.outHeight;
                    cropData.x = (this.width - cropData.width)/2;
                }
            }
        } else {
            this._cropRegionChanged = true;
        }
        this.cropData = cropData;
        this._image.setPosition(cropData.x, cropData.y);
        this._resizer.preserveRatio = preserveRatio;
        if (cropData.width != null && cropData.height != null) {
            this._ignoreRegionChangeAction = true;
            this._resizer.resizeTo(cropData.width, cropData.height);
        }
    },

    _setImageSize: function (width, height)
    {
        this._wrapEl.setSize(width, height);
        this.setSize(width+14, height + 69);
        this._image.width = width;
        this._image.height = height;
        this._resizer.maxWidth = width;
        this._resizer.maxHeight = height;
    },

    _styleHandles: function() {
        if (this._centerHandle) {
            if (!(this._centerHandle instanceof Ext.XTemplate)) {
                this._centerHandle = new Ext.XTemplate(this._centerHandle);
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

Ext.reg('kwc.image.cropimage', Kwc.Abstract.Image.CropImage);
