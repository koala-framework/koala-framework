Kwf.onJContentReady(function(){
    if(window.location.hash) {
        var el = $(document).find(window.location.hash);
        if(el && el.length) {
            $(window).scrollTop($(el).offset().top);
        }
    }
}, this, {priority: 50});