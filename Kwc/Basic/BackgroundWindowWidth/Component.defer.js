var $ = require('jquery');
var onReady = require('kwf/commonjs/on-ready');

onReady.onResize('.kwcClass', function(el) {
    el.find('.kwcBem__background').each(function(i, bg){
        $(bg).css('height', $(bg).parent().height());
    });
}, { priority: 15 }); //after Fade.Elements
