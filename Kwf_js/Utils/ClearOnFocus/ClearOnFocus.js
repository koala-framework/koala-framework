var onReady = require('kwf/commonjs/on-ready-ext2');

onReady.onRender('.kwfUp-kwfClearOnFocus', function(xel)
{
    var Event = Ext2.EventManager;

        el = xel.dom;
        if (el.value == '') return;

        var initText = el.value;
        xel.addClass('kwfUp-kwfClearOnFocusBlurred');

        Event.on(el, 'focus', function() {
            if (this.value == '' || this.value == initText) {
                Ext2.fly(this).removeClass('kwfUp-kwfClearOnFocusBlurred');
                this.value = '';
            }
        }, el);

        Event.on(el, 'blur', function() {
            if (this.value == '') {
                this.value = initText;
                Ext2.fly(this).addClass('kwfUp-kwfClearOnFocusBlurred');
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
