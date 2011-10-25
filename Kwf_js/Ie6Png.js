if (Ext.isIE6) {
Kwf.onContentReady(function() {
    var images = Ext.DomQuery.select('img.kwfIe6Png');
    Ext.each(images, function(image) {
        var image = new Ext.Element(image);
        image.parent().createChild({
            style: {
                width: image.getWidth() + 'px',
                height: image.getHeight() + 'px',
                filter: 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''+image.dom.src+'\')'
            }
        }, image);
        image.remove();
    });
});
}