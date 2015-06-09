Kwf.onElementReady('.kwfClearOnFocus', function(xel)
{
    var Event = Ext2.EventManager;

        el = xel.dom;
        if (el.value == '') return;

        var initText = el.value;
        xel.addClass('kwfClearOnFocusBlurred');

        Event.on(el, 'focus', function() {
            if (this.value == '' || this.value == initText) {
                Ext2.fly(this).removeClass('kwfClearOnFocusBlurred');
                this.value = '';
            }
        }, el);

        Event.on(el, 'blur', function() {
            if (this.value == '') {
                this.value = initText;
                Ext2.fly(this).addClass('kwfClearOnFocusBlurred');
            }
        }, el);

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
