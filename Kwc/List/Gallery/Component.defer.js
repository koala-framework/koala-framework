Kwf.onJElementReady('.cssClass', function(el) {
    var button = el.children('.moreButton');

    el.addClass('hidePics');

    button.on('click', function(event) {
        button.hide();
        el.removeClass('hidePics');

        Kwf.callOnContentReady(el, {action: 'show'});
    });
});
