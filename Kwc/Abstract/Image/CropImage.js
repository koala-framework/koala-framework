Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.CropImage = Ext.extend(Ext.BoxComponent, {
    minWidth: 50,
    minHeight: 50,
    preserveRatio: false,
    image: null,

    autoEl: {
        tag: 'div',
        children: [{
            tag: 'div',
            cls: 'crop-image-wrapper',
            style: {
                background: '#ffffff',
                opacity: 0.5,
                position: 'absolute'
            }
        }]
    },

    initComponent: function() {
        Kwc.Abstract.Image.CropImage.superclass.initComponent.call(this);
    },

    getCropData: function() {
        var parent = this.getBox();
        if (!this.image) {
            return;
        }
        var image = this.image.getBox();
        var result = {
            x: (image.x - parent.x),
            y: (image.y - parent.y),
            width: image.width,
            height: image.height
        };
        this.image.getEl().setStyle({
            'background-position': (-result.x)+'px '+(-result.y)+'px'
        });
        return result;
    },

    onRender: function(ct, position) {
        Kwc.Abstract.Image.CropImage.superclass.onRender.call(this, ct, position);
        this.el.setStyle({
            'background': 'url('+this.src+') no-repeat left top'
        });

        var wrapEl = this.el.down('.crop-image-wrapper');
        wrapEl.setSize(this.width, this.height);

        this.image = new Ext.BoxComponent({
            opacity: 1.0,
            autoEl: {
                tag: 'div',
                src: this.src
            },
            renderTo: this.el,
            constrainTo: this.el,
            src: Ext.BLANK_IMAGE_URL,
            x: this.cropX +7,//to be correct placed.
            y: this.cropY, //no problems in y-direction
            height: this.height,
            width: this.width,
            style:{
              cursor: 'move',
              position: 'absolute',
              background: 'url('+this.src+') no-repeat left top'
            }
        });
        this.getCropData();

        var resizer = new Ext.Resizable(this.image.getEl(), {
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
        resizer.on("resize", function() {
            var res = me.getCropData();
            this.fireEvent('changeCrop', this, res);
            this.fireEvent('resizeCrop', this, res);
        }, this);

        var me = this;
        var dragDrop = new Ext.dd.DD(this.image.getEl(), '');
        dragDrop.startDrag = function (x, y) {
            dragDrop.constrainTo(me.el);
            me.image.getEl().setStyle({
                'background': 'transparent'
            });
        };
        dragDrop.endDrag  = function (e) {
            me.image.getEl().setStyle({
                'background-image': 'url('+me.src+')',
                'background-repeat': 'no-repeat'
            });
            me.fireEvent('changeCrop', me, me.getCropData());
            me.fireEvent('moveCrop', me, me.getCropData());
        };
    }
});

Ext.reg('kwc.image.cropimage', Kwc.Abstract.Image.CropImage);
