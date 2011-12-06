Kwf.onContentReady(function() {
    // alle multicheckboxes holen
    var multiCheckboxes = Ext.query('.kwfFormFieldMultiCheckbox');
    Ext.each(multiCheckboxes, function(mc) {
        mc = Ext.get(mc);
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
    });
});

Kwf.FrontendForm.MultiCheckbox = Ext.extend(Kwf.FrontendForm.Field, {
    initField: function() {
    },
    getValue: function() { //has no value itself
        return null;
    },
    getFieldName: function() {
        var m = this.el.dom.className.match(/ ([^ ]+) *$/);
        return m[1];
    }
});

Kwf.FrontendForm.fields['kwfFormFieldMultiCheckbox'] = Kwf.FrontendForm.MultiCheckbox;

