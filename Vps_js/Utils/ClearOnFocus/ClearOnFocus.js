Vps.onContentReady(function()
{
    var Event = Ext.EventManager;

    var els = Ext.query('input.vpsClearOnFocus');
    els.forEach(function(el) {
        if (!el || el.value == '') return;
        var xel = Ext.get(el);

        var initText = el.value;
        xel.addClass('vpsClearOnFocusBlurred');

        Event.on(el, 'focus', function() {
            if (el.value == '' || el.value == initText) {
                xel.removeClass('vpsClearOnFocusBlurred');
                el.value = '';
            }
        });

        Event.on(el, 'blur', function() {
            if (el.value == '') {
                el.value = initText;
                xel.addClass('vpsClearOnFocusBlurred');
            }
        });

        // form ermitteln und clearOnFocus value nicht mitsenden
        var elForm = el.parentNode;
        while (elForm.tagName != 'FORM') {
            if (elForm.tagName == 'BODY') {
                elForm = false;
                break;
            }
            elForm = elForm.parentNode;
        }

        if (elForm != false && elForm.tagName == 'FORM') {
            Event.on(elForm, 'submit', function() {
                if (el.value == initText) el.value = '';
            });
        }
    });
});
