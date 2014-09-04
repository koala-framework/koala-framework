Kwf.onJElementWidthChange('.kwcBasicTable.responsiveFlipScroll', function(el) {
    var addArrow = function(el) {
        if (el.hasClass('flipScroll')) {
            var tbody = el.children('tbody');
            if (tbody.scrollLeft() == 0 && tbody.width() > el.width()) {
                el.addClass('arrowRight');
            } else {
                el.removeClass('arrowRight');
            }
        }
    };

    if (el.hasClass('flipScroll')) {
        if (el.width() > el.data('flipScrollSetAt')) {
            el.removeClass('flipScroll');
        }
    }
    if (el.width() < el.children('table').width() && !el.hasClass('flipScroll')) {
        el.addClass('flipScroll');
        addArrow(el);
        if (!el.flipScrollSetAt) {
            el.children('tbody').on('scroll', function(ev) {
                addArrow($(this).closest('.kwcBasicTable'));
            });
        }
        el.data('flipScrollSetAt', el.width());
    }
});
