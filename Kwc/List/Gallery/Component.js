var onReady = require('kwf/on-ready');
var responsiveEl = require('kwf/responsive-el');
responsiveEl('.cssClass', [600, 360]);

onReady.onRender('.cssClass', function(el) {
    var button = el.children('.moreButton');
    var hiddenPics = el.children('.morePics');

    hiddenPics.hide();

    button.on('click', function(event) {
        button.hide(300, 'swing');
        hiddenPics.show();
        Kwf.callOnContentReady(hiddenPics, {action: 'show'});
        hiddenPics.hide();
        hiddenPics.show(300, 'swing');
    });
});
