Vps.onContentReady(function() {
    var galleries = Ext.query('.vpsEnlargeNextPrevious');
    Ext.each(galleries, function(gallery) {
        var galleryEls = [ ];

        var els = Ext.query('a', gallery);
        Ext.each(els, function(el) {
            if (el && el.rel.match(/enlarge_[0-9]+_[0-9]+/)) {
                galleryEls.push(Ext.get(el));
            }
        });

        for (var i = 0; i < galleryEls.length; i++) {
            // gibts ein vorheriges image?
            if (galleryEls[i-1]) {
                galleryEls[i].previousImage = galleryEls[i-1];
            }

            // gibts ein nächstes image?
            if (galleryEls[i+1]) {
                galleryEls[i].nextImage = galleryEls[i+1];
            }
        }
    });
});
