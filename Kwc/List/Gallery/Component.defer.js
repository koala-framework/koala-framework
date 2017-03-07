var $ = require('jquery');
var onReady = require('kwf/commonjs/on-ready');

onReady.onRender('.kwcClass', function(el) {
    var button = el.children('.moreButton');

    el.addClass('hidePics');

    button.on('click', function(event) {
        button.hide();
        el.removeClass('hidePics');

        onReady.callOnContentReady(el, {action: 'show'});
    });
});
