Kwf.Utils.ResponsiveEl('.redMalleeMenuMain', [1100, 960, 700, 659, 500]);
Kwf.Utils.ResponsiveEl('body', [1100, 960, 700, 659, 500]);

$(function(){
    var selector = $('.redMalleeMenuMain ul li.hasSubmenu');
    var dropdown = $('.dropdown');
    var mask = $('#mask');

    $(selector).mouseover(function() {
        if (window.innerWidth > 979) {
            mask.css('display', 'block');
        }
    });
    $(selector).mouseout(function() {
        mask.css('display', 'none');
    });
});

