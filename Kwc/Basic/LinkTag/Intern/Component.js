Kwf.onJElementReady('a', function anchorLinks(el) {
    var pos = $(el).attr('href') ? $(el).attr('href').indexOf('#') : null;
    if (pos > 0 && $(el).attr('href').substr(0, 1) == '/') {
        var anchor = $(el).attr('href').substr(pos);
        if (anchor.match(/^#[a-z0-9_-]+$/i)) {
            var target = $(anchor);
            if (target && target.length) {
                $(el).click(function(e){
                    e.preventDefault();
                    $('html, body').stop().animate({scrollTop: target.offset().top}, 500, function() {
                        window.location.hash = $(el).attr('href').substr(pos);
                    });
                });
            }
        }
    }
}, {defer: true});
