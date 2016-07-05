var $ = require('jQuery');
var onReady = require('kwf/on-ready');

onReady.onResize('.kwcClass', function(el) {
    el.find('.kwcBem__background').each(function(i, bg){
        $(bg).css('height', $(bg).parent().height());
    });
}, { priority: 15 }); //after Fade.Elements
