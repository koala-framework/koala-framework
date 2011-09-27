Vps.onContentReady(function() {
    // alle multicheckboxes holen
    var multiCheckboxes = Ext.query('.vpsFormFieldMultiCheckbox');
    Ext.each(multiCheckboxes, function(mc) {
        mc = Ext.get(mc);
        var checkAll = mc.child('a.vpsMultiCheckboxCheckAll');
        var checkNone = mc.child('a.vpsMultiCheckboxCheckNone');

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
