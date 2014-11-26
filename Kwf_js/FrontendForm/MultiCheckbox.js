Kwf.onElementReady('.kwfFormFieldMultiCheckbox', function multiCheckbox(mc) {
        var checkAll = mc.child('a.kwfMultiCheckboxCheckAll');
        var checkNone = mc.child('a.kwfMultiCheckboxCheckNone');

        if (checkAll) {
            checkAll.on('click', function(ev) {
                ev.stopEvent();
                var allInputs = this.query('input');
                for (var i = 0; i < allInputs.length; i++) {
                    if (allInputs[i].type == 'checkbox') {
                        allInputs[i].checked = true;
                    }
                }
            }, mc);
        }
        if (checkNone) {
            checkNone.on('click', function(ev) {
                ev.stopEvent();
                var allInputs = this.query('input');
                for (var i = 0; i < allInputs.length; i++) {
                    if (allInputs[i].type == 'checkbox') {
                        allInputs[i].checked = false;
                    }
                }
            }, mc);
        }
}, { defer: true });

Kwf.FrontendForm.MultiCheckbox = Ext2.extend(Kwf.FrontendForm.Field, {
    initField: function() {
    },
    getValue: function() { //has no value itself
        return null;
    },
    clearValue: function() {
    },
    setValue: function() {
    },
    getFieldName: function() {
        return this.el.child('.kwfFormFieldMultiCheckbox').dom.getAttribute('data-fieldname');
    }
});

Kwf.FrontendForm.fields['kwfFormFieldMultiCheckbox'] = Kwf.FrontendForm.MultiCheckbox;

