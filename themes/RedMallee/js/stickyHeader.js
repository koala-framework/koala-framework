$(function(){
    var mainMenu = $('.mainMenu').offset().top;
    $(window).scroll(function(){
        if( $(window).scrollTop() > (mainMenu-30) ) {
            $('#outerHeader').addClass("sticky");
            $('body').addClass("sticky");
            $('#outerHeader').removeClass("notSticky");
            $('.sticky .logo img').attr({
                width: 100,
                height: 67
            });
        } else {
            $('#outerHeader').removeClass("sticky");
            $('body').removeClass("sticky");
            $('#outerHeader').addClass("notSticky");
            $('.notSticky .logo img').attr({
                width: 180,
                height: 100
            });
        }
    });
});