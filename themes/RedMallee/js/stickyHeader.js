$(function(){
    var mainMenu = $('.mainMenu').offset().top;
    $(window).scroll(function(){
        if( $(window).scrollTop() > (mainMenu-30) ) {
            $('#outerHeader').addClass("sticky");
            $('body').addClass("sticky");
            $('#outerHeader').removeClass("notSticky");
            $('.sticky .logo .redMalleeBoxLogo img').height(67);
            $('.sticky .logo .redMalleeBoxLogo img').width(100);
        } else {
            $('#outerHeader').removeClass("sticky");
            $('body').removeClass("sticky");
            $('#outerHeader').addClass("notSticky");
            $('.notSticky .logo .redMalleeBoxLogo img').removeStyle('height');
            $('.notSticky .logo .redMalleeBoxLogo img').removeStyle('width');

        }
    });
});

(function($)
{
    $.fn.removeStyle = function(style)
    {
        var search = new RegExp(style + '[^;]+;?', 'g');

        return this.each(function()
        {
            $(this).attr('style', function(i, style)
            {
                return style.replace(search, '');
            });
        });
    };
}(jQuery));