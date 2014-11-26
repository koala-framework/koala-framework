$(document).on('click', 'a', function(e) {
    var el = e.currentTarget;
    var pos = $(el).attr('href').indexOf('#');
    if (pos != -1) {
        if (window.location.pathname === $(el).attr('href').substr(0, pos)) {
            var anchor = $(el).attr('href').substr(pos);
            if (anchor.match(/^#[a-z0-9_-]+$/i)) {
                var target = $(anchor);
                if (target && target.length) {
                    $('html, body').stop().animate({scrollTop: target.offset().top}, 500, function() {
                        window.location.hash = $(el).attr('href').substr(pos);
                    });
                    e.preventDefault();
                }
            }
        }
    }
});
