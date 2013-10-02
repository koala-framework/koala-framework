Ext.namespace('Kwc.Abstract.Image');
Kwc.Abstract.Image.CropWindow = Ext.extend(Ext.Window, {
    cropData: null,
    imageUrl: '',
    resizable: false,
    title: 'Image Crop Utility',
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
                    text: 'cancel',
                    itemId: 'cancelButton'
                },
                {
                    xtype: 'button',
                    text: 'save',
                    itemId: 'saveButton'
                }
            ]
        };

        Kwc.Abstract.Image.CropWindow.superclass.initComponent.call(this);

        var me = this; //Using var me because it's not possible to use scope another way
        // tried to capsule imgLoad in Ext.get()-Element and work with on('load', function , scope) but didn't work
        var imgLoad = new Image();
        imgLoad.onerror = (function() {
            //TODO implement user-error-notification
            //TODO implement mask while loading
        });
        imgLoad.onload = (function(){
            me.setSize(imgLoad.width+14, imgLoad.height + 69);
            var cropWidth, cropHeight, cropX = 0, cropY = 0;
            if (me.cropData) {
                cropX = me.cropData.x;
                cropY = me.cropData.y;
                cropWidth = me.cropData.width;
                cropHeight = me.cropData.height;
            } else {
                cropWidth = imgLoad.width;
                cropHeight = imgLoad.height;

                if (me.preserveRatio) {
                    if (cropHeight / me.outHeight > cropWidth / me.outWidth) {
                        // orientate on width
                        cropHeight = me.outHeight * cropWidth / me.outWidth;
                        cropY = (imgLoad.height - cropHeight)/2;
                    } else {
                        // orientate on height
                        cropWidth = me.outWidth * cropHeight / me.outHeight;
                        cropX = (imgLoad.width - cropWidth)/2;
                    }
                }
            }

            me.cropData = {
                x: cropX,
                y: cropY,
                width: cropWidth,
                height: cropHeight
            };
            var crop = new Kwc.Abstract.Image.CropImage({
                src: me.imageUrl,
                preserveRatio: me.preserveRatio,
                width: imgLoad.width,
                height: imgLoad.height,
                cropWidth: cropWidth,
                cropHeight: cropHeight,
                cropX: cropX,
                cropY: cropY
            });

            crop.on('changeCrop', function(foo,x) {
                me.fireEvent('changeCrop', foo, x);
                me.cropData = x;
            }, me);
            me.add(crop);
            me.doLayout();
        });
        imgLoad.src = me.imageUrl;
    }
});

Ext.reg('kwc.image.cropwindow', Kwc.Abstract.Image.CropWindow);
