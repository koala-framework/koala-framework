var $ = require('jquery');
$(document).on('click', 'a.kwfUp-kwcLinkIntern', function(e) {
    var el = e.currentTarget;
    var href = $(el).attr('href');
    if (href != undefined) {
        var pos = href.indexOf('#');
        if (pos != -1) {
            if (window.location.pathname === href.substr(0, pos)) {
                var anchor = href.substr(pos);
                if (anchor.match(/^#[a-z0-9_-]+$/i)) {
                    var target = $(anchor);
                    if (target && target.length) {
                        $('html, body').stop().animate({scrollTop: target.offset().top}, 500, function() {
                            window.location.hash = href.substr(pos);
                        });
                        e.preventDefault();
                    }
                }
            }
        }
    }
});
