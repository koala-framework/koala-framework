Kwf.Utils.ResponsiveEl('.redMalleeMenuMain', [1100, 960, 700, 659, 500]);
Kwf.Utils.ResponsiveEl('body', [1100, 960, 700, 659, 500]);

$(function(){
    var selector = $('.redMalleeMenuMain > ul > li.hasSubmenu');
    var dropdown = $('.dropdown');
    var mask = $('#mask');


    $(selector).mouseenter(function() {
        if (window.innerWidth > 979) {
            mask.stop().fadeIn(400);
        }
    });
    $(selector).mouseleave(function() {
        mask.stop().fadeOut(400);
    });
});

