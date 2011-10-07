Vps.onContentReady(function() {
    var components = Ext.query('a.vpcEnlargeLightbox');
    Ext.each(components, function(c) {
        var link = Ext.get(c);
        var size = link.dom.rel.match(/size_([0-9]+)_([0-9]+)/);
        var id = link.dom.rel.match(/id_([\w\-]*)/);
        var settings = {
            group: false,
            width: parseInt(size[1]) + 30,
            height: parseInt(size[2]) + 30,
            inline: true,
            href: '#vpcEnlargeLightbox-' + id[1]
        };
        if (!link.lightboxProcessed) {
            link.lightboxProcessed = true;
            link.on('click', function (ev) {
                ev.preventDefault();
                Vps.Lightbox.Lightbox.open(link.dom, null, settings);
            });
            Ext.DomHelper.append(link,
                { tag: 'span', cls: 'webZoom' }
            );
        }
    });
});