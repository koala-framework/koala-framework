Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.CropWindow = Ext.extend(Ext.Window, {
    cropData: null,
    imageUrl: '',
    resizable: false,
    title: trlKwf('Image Crop Utility'),
    width: 660,
    height: 510,
    modal: true,
    preserveRatio: false,

    initComponent: function() {
        this.fbar = {
            xtype: 'toolbar',
            items: [
                {
                    xtype: 'button',
                    text: trlKwf('Cancel'),
                    itemId: 'cancelButton'
                },
                {
                    xtype: 'button',
                    text: trlKwf('Save'),
                    itemId: 'saveButton'
                }
            ]
        };

        Kwc.Abstract.Image.CropWindow.superclass.initComponent.call(this);

        Ext.getBody().mask(trlKwf('Loading image'), 'x-mask-loading');
        var imgLoad = new Image();
        imgLoad.onerror = (function() {
            Ext.getBody().unmask();
            Ext.Msg.alert(trlKwf('Error'), trlKwf('Couldn\'t load image.'));
        });
        imgLoad.onload = (function(){
            Ext.getBody().unmask();
            this.setSize(imgLoad.width+14, imgLoad.height + 69);
            var cropWidth, cropHeight, cropX = 0, cropY = 0;
            if (this.cropData) {
                cropX = this.cropData.x;
                cropY = this.cropData.y;
                cropWidth = this.cropData.width;
                cropHeight = this.cropData.height;
            } else {
                cropWidth = imgLoad.width;
                cropHeight = imgLoad.height;

                if (this.preserveRatio) {
                    if (cropHeight / this.outHeight > cropWidth / this.outWidth) {
                        // orientate on width
                        cropHeight = this.outHeight * cropWidth / this.outWidth;
                        cropY = (imgLoad.height - cropHeight)/2;
                    } else {
                        // orientate on height
                        cropWidth = this.outWidth * cropHeight / this.outHeight;
                        cropX = (imgLoad.width - cropWidth)/2;
                    }
                }
            }

            this.cropData = {
                x: cropX,
                y: cropY,
                width: cropWidth,
                height: cropHeight
            };
            var crop = new Kwc.Abstract.Image.CropImage({
                src: this.imageUrl,
                preserveRatio: this.preserveRatio,
                width: imgLoad.width,
                height: imgLoad.height,
                cropWidth: cropWidth,
                cropHeight: cropHeight,
                cropX: cropX,
                cropY: cropY
            });

            crop.on('changeCrop', function(cropImageElement, x) {
                this.cropData = x;
            }, this);
            this.add(crop);
            this.doLayout();
        }).createDelegate(this);
        imgLoad.src = this.imageUrl;
    }
});

Ext.reg('kwc.image.cropwindow', Kwc.Abstract.Image.CropWindow);
