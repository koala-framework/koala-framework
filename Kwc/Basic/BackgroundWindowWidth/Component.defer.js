Kwf.onJElementWidthChange('.cssClass', function(el) {
    el.find('.background').each(function(i, bg){
        $(bg).css('height', $(bg).parent().height());
    });
}, { priority: 15 }); //after Fade.Elements
