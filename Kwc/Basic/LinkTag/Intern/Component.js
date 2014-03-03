Kwf.onElementReady('.kwcBasicLinkTagIntern', function(el){
    var pos = el.dom.href.indexOf('#');
    if (pos > 0 && el.dom.href.substr(0, pos) === location.href.substr(0, pos)) {
        var target = $(el.dom.href.substr(pos));
        if (target && target.length) {
            $(el.dom).click(function(e){
                e.preventDefault();
                $('html, body').stop().animate({scrollTop: target.offset().top}, 500, function() {
                    window.location.hash = el.dom.href.substr(pos);
                });
            })
        }
    }
});