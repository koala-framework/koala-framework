Kwf.onElementReady('.kwcBasicLinkTagIntern', function(el) {
    var pos = el.dom.href.indexOf('#');
    if (pos > 0 && el.dom.href.substr(0, pos) == location.href) {
        var target = $(el.dom.href.substr(pos));
        $(el.dom).click(function(e){
            $('html,body').stop().animate(
                {scrollTop: target.offset().top},
                {easing: 'swing', duration: 500}
            );
        });
    }
});
