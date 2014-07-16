Kwf.onContentReady(function linkTagIntern(el){
    var links = $(el).find('a');

    if(links && links.length) {
        $.each(links, function(i, link){
            if($(link).data('checkIntern')) return;
            $(link).data('checkIntern', true);
            var pos = $(link).attr('href') ? $(link).attr('href').indexOf('#') : null;
            if (pos > 0 && $(link).attr('href').substr(0, 1) == '/') {
                var target = $($(link).attr('href').substr(pos));
                if (target && target.length) {
                    $(link).click(function(e){
                        e.preventDefault();
                        $('html, body').stop().animate({scrollTop: target.offset().top}, 500, function() {
                            window.location.hash = $(link).attr('href').substr(pos);
                        });
                    });
                }
            }
        });
    }
}, { defer: true });
