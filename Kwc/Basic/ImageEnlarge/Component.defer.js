Kwf.onElementReady('.cssClass.showHoverIcon > a', function imageEnlarge(el) {
    if (el.getWidth() > 50 && el.getHeight() > 50) {
        el.createChild({ tag: 'span', cls: 'outerHoverIcon', html: '<span class="innerHoverIcon"></span>'});
        if (el.getWidth() < 200) {
            el.addClass('small');
        }
    }
}, { checkVisibility: true, defer: true });
