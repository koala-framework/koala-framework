var onReady = require('kwf/on-ready');

onReady.onResize('.cssClass', function(el) {
    el.find('.background').each(function(i, bg){
        $(bg).css('height', $(bg).parent().height());
    });
}, { priority: 15 }); //after Fade.Elements
