Kwf.onContentReady(function() {
    if ($('.mainMenu').length) {
        var mainMenu = $('.mainMenu').offset().top;
        $(window).scroll(function(){
            if( $(window).scrollTop() > (mainMenu-45) && ($(window).width() > 1100)) {
                $('#outerHeader').addClass("sticky");
                $('body').addClass("sticky");
                $('#outerHeader').removeClass("notSticky");
            } else {
                $('#outerHeader').removeClass("sticky");
                $('body').removeClass("sticky");
                $('#outerHeader').addClass("notSticky");
            }
        });
    }
});
