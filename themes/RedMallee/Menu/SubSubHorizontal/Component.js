Kwf.onElementReady('.redMalleeMenuSubSubHorizontal', function(el) {
    var menu = el.child('.menu').dom;
    if (menu.scrollWidth - $(menu).innerWidth() == 0) return false;
    $(menu).before('<a class="arrowLeft"></a>').before('<a class="arrowRight"></a>');
    var arrowLeft = $(el.child('.arrowLeft').dom).hide();
    var arrowRight = $(el.child('.arrowRight').dom);
    arrowLeft.bind('click', function() {
        $(menu).animate({
            scrollLeft: 0
        }, 500);
    });
    arrowRight.bind('click', function() {
        $(menu).animate({
            scrollLeft: menu.scrollWidth - $(menu).innerWidth()
        }, 500);
    });
    $(menu).scroll(function(event) {
        if ($(this).scrollLeft() == 0) {
            if (arrowLeft.is(':visible')) arrowLeft.fadeOut();
            if (!arrowRight.is(':visible')) arrowRight.fadeIn();
        } else if ($(this).scrollLeft() == (menu.scrollWidth - $(menu).innerWidth())) {
            if (!arrowLeft.is(':visible')) arrowLeft.fadeIn();
            if (arrowRight.is(':visible')) arrowRight.fadeOut();
        } else {
            if (!arrowLeft.is(':visible')) arrowLeft.fadeIn();
            if (!arrowRight.is(':visible')) arrowRight.fadeIn();
        }
    });
});
