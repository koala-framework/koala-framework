Kwf.onJElementReady('.kwcMenuScrollButtons', function(el) {
    var menu = $(el).find('.menu');

    $(menu).before('<a class="arrowLeft"></a>').before('<a class="arrowRight"></a>');

    var arrowLeft = $(el).find('.arrowLeft').hide();
    var arrowRight = $(el).find('.arrowRight').hide();

    $(arrowLeft).on('click', function(e) {
        $(menu).animate({
            scrollLeft: 0
        }, 500);
    });

    $(arrowRight).on('click', function(e) {
        $(menu).animate({
            scrollLeft: menu.get(0).scrollWidth - $(menu).innerWidth()
        }, 500);
    });

    $(menu).on('scroll', function(event) {
        if ($(this).scrollLeft() == 0) {
            if (arrowLeft.is(':visible')) arrowLeft.fadeOut();
            if (!arrowRight.is(':visible')) arrowRight.fadeIn();
        } else if ($(this).scrollLeft() == (menu.get(0).scrollWidth - $(menu).innerWidth())) {
            if (!arrowLeft.is(':visible')) arrowLeft.fadeIn();
            if (arrowRight.is(':visible')) arrowRight.fadeOut();
        } else {
            if (!arrowLeft.is(':visible')) arrowLeft.fadeIn();
            if (!arrowRight.is(':visible')) arrowRight.fadeIn();
        }
    });

    function adjustMenu() {
        if (menu.get(0).scrollWidth - $(menu).innerWidth() == 0) {
            $(arrowLeft).hide();
            $(arrowRight).hide();
        } else {
            $(arrowRight).show();
        }
    }

    $(window).resize(function(e){
        adjustMenu();
    }) 

    adjustMenu();
});
