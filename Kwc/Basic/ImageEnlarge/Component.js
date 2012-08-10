Kwf.onElementReady('.kwcBasicImageEnlarge.showHoverIcon > a', function(el) {
    if (el.getWidth() > 100 && el.getHeight() > 100) {
        el.createChild({ tag: 'span', cls: 'outerHoverIcon', html: '<span class="innerHoverIcon"></span>'});
    }
});
