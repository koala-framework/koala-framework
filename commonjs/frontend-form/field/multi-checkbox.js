var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

var onReady = require('kwf/on-ready');

onReady.onRender('.kwfFormFieldMultiCheckbox', function multiCheckbox(mc) {
        var checkAll = mc.find('a.kwfMultiCheckboxCheckAll');
        var checkNone = mc.find('a.kwfMultiCheckboxCheckNone');

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

var MultiCheckbox = kwfExtend(Field, {
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
        return this.el.find('.kwfFormFieldMultiCheckbox').get(0).getAttribute('data-fieldname');
    }
});

fieldRegistry.register('kwfFormFieldMultiCheckbox', MultiCheckbox);
module.exports = MultiCheckbox;

