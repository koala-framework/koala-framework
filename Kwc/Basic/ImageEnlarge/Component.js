Kwf.onElementReady('.kwcBasicImageEnlarge.showHoverIcon > a', function(el) {
    if (el.getWidth() > 50 && el.getHeight() > 50) {
        el.createChild({ tag: 'span', cls: 'outerHoverIcon', html: '<span class="innerHoverIcon"></span>'});
    }
});
