Kwf.Utils.ResponsiveEl('.kwcListGallery', [600, 360]);

Kwf.onJElementReady('.cssClass', function(el) {
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
