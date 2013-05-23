Kwf.onElementReady('.kwcBasicImageEnlarge.showHoverIcon > a', function(el) {
    var imgEl = el.child('img');
    if (imgEl) {
        var img = imgEl.dom;
        if (img.width > 50 && img.height > 50) {
            el.createChild({ tag: 'span', cls: 'outerHoverIcon', html: '<span class="innerHoverIcon"></span>'});
            if (img.width < 200) {
                el.addClass('small');
            }
        }
    }
});
