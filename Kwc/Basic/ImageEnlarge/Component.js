Kwf.onElementReady('.kwcBasicImageEnlarge.showHoverIcon > a', function(el) {
    var img = el.child('img').dom;
    if (img.width > 50 && img.height > 50) {
        el.createChild({ tag: 'span', cls: 'outerHoverIcon', html: '<span class="innerHoverIcon"></span>'});
    }
});
