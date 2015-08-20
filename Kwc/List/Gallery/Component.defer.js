var $ = require('jQuery');
var onReady = require('kwf/on-ready');

onReady.onRender('.kwcClass', function(el) {
    var button = el.children('.moreButton');

    el.addClass('hidePics');

    button.on('click', function(event) {
        button.hide();
        el.removeClass('hidePics');

        Kwf.callOnContentReady(el, {action: 'show'});
    });
});
