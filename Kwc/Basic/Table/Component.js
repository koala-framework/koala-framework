Kwf.Utils.ResponsiveEl('.kwcBasicTable.responsiveFlipScroll', function(el) {

    var addArrow = function(el) {
        if (el.hasClass('flipScroll')) {
            var tbody = el.child('tbody');
            if (tbody.dom.scrollLeft == 0 && tbody.dom.scrollWidth > el.getWidth()) {
                el.addClass('arrowRight');
            } else {
                el.removeClass('arrowRight');
            }
        }
    };

    if (el.hasClass('flipScroll')) {
        if (el.getWidth() > el.dom.flipScrollSetAt) {
            el.removeClass('flipScroll');
        }
    }
    if (el.getWidth() < el.child('table').getWidth() && !el.hasClass('flipScroll')) {
        el.addClass('flipScroll');
        addArrow(el);
        if (!el.dom.flipScrollSetAt) {
            el.child('tbody').on('scroll', function(ev) {
                addArrow(Ext2.fly(ev.getTarget()).up('.kwcBasicTable'));
            });
        }
        el.dom.flipScrollSetAt = el.getWidth();
    }


});
