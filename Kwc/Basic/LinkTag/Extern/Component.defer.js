$(document).on('click', 'a', function(event) {
    var lnk = event.currentTarget;

    //for backwards compatibility
    var rels = lnk.rel.split(' ');
    $.each(rels, function() {
        if (this.match(/^popup/)) {
            var relProperties = this.split('_');
            //$(lnk).addClass('webLinkPopup');
            if (relProperties[1] == 'blank') {
                window.open(lnk.href, '_blank');
            } else {
                window.open(lnk.href, '_blank', relProperties[1]);
            }
            event.preventDefault();
        }
    });

    if ($(lnk).data('kwc-popup')) {
        if ($(lnk).data('kwc-popup') == 'blank') {
            window.open(lnk.href, '_blank');
        } else {
            window.open(lnk.href, '_blank', $(lnk).data('kwc-popup'));
        }
    }
});
